<?php

use OscarWeijman\PhpOllama\Client;
use OscarWeijman\PhpOllama\Model;
use OscarWeijman\PhpOllama\Response\Response;
use OscarWeijman\PhpOllama\Response\StreamResponse;

test('model has correct name', function () {
    $client = Mockery::mock(Client::class);
    $model = new Model($client, 'llama2');
    
    expect($model->getName())->toBe('llama2');
});

test('model can generate completions', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')->andReturn(['response' => 'Test response']);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('generate')
        ->with('llama2', 'Test prompt', [], false, null)
        ->andReturn($mockResponse);
    
    $model = new Model($client, 'llama2');
    $response = $model->generate('Test prompt');
    
    expect($response)->toBe($mockResponse);
});

test('model can generate streaming completions', function () {
    $mockStreamResponse = Mockery::mock(StreamResponse::class);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('generate')
        ->with('llama2', 'Test prompt', [], true, Mockery::type('callable'))
        ->andReturn($mockStreamResponse);
    
    $model = new Model($client, 'llama2');
    $callback = function ($chunk) {};
    $response = $model->generate('Test prompt', [], true, $callback);
    
    expect($response)->toBe($mockStreamResponse);
});

test('model can chat', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')->andReturn(['message' => ['content' => 'Chat response']]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('chat')
        ->with('llama2', [['role' => 'user', 'content' => 'Hello']], [], false, null)
        ->andReturn($mockResponse);
    
    $model = new Model($client, 'llama2');
    $response = $model->chat([['role' => 'user', 'content' => 'Hello']]);
    
    expect($response)->toBe($mockResponse);
});

test('model can get info', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')->andReturn([
        'name' => 'llama2',
        'modelfile' => 'FROM llama2...',
    ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('getModel')
        ->with('llama2')
        ->andReturn($mockResponse);
    
    $model = new Model($client, 'llama2');
    $info = $model->getInfo();
    
    expect($info)->toBe([
        'name' => 'llama2',
        'modelfile' => 'FROM llama2...',
    ]);
});
