<?php

use OscarWeijman\PhpOllama\Client;
use OscarWeijman\PhpOllama\Embeddings\Embeddings;
use OscarWeijman\PhpOllama\Response\Response;

test('embeddings can be created with model', function () {
    $client = Mockery::mock(Client::class);
    $embeddings = new Embeddings($client, 'llama2');
    
    expect($embeddings)->toBeInstanceOf(Embeddings::class);
});

test('embeddings can generate embeddings for text', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')->andReturn([
        'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
    ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('embeddings')
        ->with('llama2', 'Test text', [])
        ->andReturn($mockResponse);
    
    $embeddings = new Embeddings($client, 'llama2');
    $result = $embeddings->embed('Test text');
    
    expect($result)->toBe([
        'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
    ]);
});

test('embeddings can generate batch embeddings', function () {
    $mockResponse1 = Mockery::mock(Response::class);
    $mockResponse1->shouldReceive('getData')->andReturn([
        'embedding' => [0.1, 0.2, 0.3],
    ]);
    
    $mockResponse2 = Mockery::mock(Response::class);
    $mockResponse2->shouldReceive('getData')->andReturn([
        'embedding' => [0.4, 0.5, 0.6],
    ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('embeddings')
        ->with('llama2', 'Text 1', [])
        ->andReturn($mockResponse1);
    $client->shouldReceive('embeddings')
        ->with('llama2', 'Text 2', [])
        ->andReturn($mockResponse2);
    
    $embeddings = new Embeddings($client, 'llama2');
    $results = $embeddings->embedBatch(['Text 1', 'Text 2']);
    
    expect($results)->toBe([
        ['embedding' => [0.1, 0.2, 0.3]],
        ['embedding' => [0.4, 0.5, 0.6]],
    ]);
});

test('embeddings can calculate cosine similarity', function () {
    $embedding1 = [1, 0, 0];
    $embedding2 = [0, 1, 0];
    $embedding3 = [1, 1, 0];
    
    // Perpendicular vectors have 0 similarity
    expect(Embeddings::cosineSimilarity($embedding1, $embedding2))->toEqual(0);
    
    // Same vector has similarity 1
    expect(Embeddings::cosineSimilarity($embedding1, $embedding1))->toEqual(1);
    
    // 45 degree angle has similarity 0.7071... (1/sqrt(2))
    $similarity = Embeddings::cosineSimilarity($embedding1, $embedding3);
    expect($similarity)->toBeGreaterThan(0.7);
    expect($similarity)->toBeLessThan(0.71);
});

test('embeddings can calculate euclidean distance', function () {
    $embedding1 = [1, 0, 0];
    $embedding2 = [0, 1, 0];
    $embedding3 = [1, 1, 0];
    
    // Distance between perpendicular unit vectors is sqrt(2)
    expect(Embeddings::euclideanDistance($embedding1, $embedding2))->toEqual(sqrt(2));
    
    // Distance to self is 0
    expect(Embeddings::euclideanDistance($embedding1, $embedding1))->toEqual(0);
    
    // Distance from [1,0,0] to [1,1,0] is 1
    expect(Embeddings::euclideanDistance($embedding1, $embedding3))->toEqual(1);
});

test('embeddings throws exception for invalid dimensions', function () {
    $embedding1 = [1, 2, 3];
    $embedding2 = [1, 2];
    
    expect(fn() => Embeddings::cosineSimilarity($embedding1, $embedding2))
        ->toThrow(\InvalidArgumentException::class);
    
    expect(fn() => Embeddings::euclideanDistance($embedding1, $embedding2))
        ->toThrow(\InvalidArgumentException::class);
});
