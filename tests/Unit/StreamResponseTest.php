<?php

use OscarWeijman\PhpOllama\Response\StreamResponse;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

test('stream response can process chunks', function () {
    // Create a memory stream with JSON chunks
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, json_encode(['response' => 'chunk1']) . "\n");
    fwrite($handle, json_encode(['response' => 'chunk2']) . "\n");
    fwrite($handle, json_encode(['response' => 'chunk3', 'done' => true]) . "\n");
    fseek($handle, 0);
    
    $stream = new Stream($handle);
    $httpResponse = new Response(200, [], $stream);
    
    $chunks = [];
    $streamResponse = new StreamResponse($httpResponse, function ($chunk) use (&$chunks) {
        $chunks[] = $chunk;
    });
    
    $result = $streamResponse->process();
    
    expect($chunks)->toHaveCount(3);
    expect($chunks[0])->toBe(['response' => 'chunk1']);
    expect($chunks[1])->toBe(['response' => 'chunk2']);
    expect($chunks[2])->toBe(['response' => 'chunk3', 'done' => true]);
    
    expect($result)->toBe(['response' => 'chunk3', 'done' => true, 'full_response' => 'chunk1chunk2chunk3']);
});

test('stream response handles empty stream', function () {
    $handle = fopen('php://memory', 'r+');
    $stream = new Stream($handle);
    $httpResponse = new Response(200, [], $stream);
    
    $streamResponse = new StreamResponse($httpResponse);
    $result = $streamResponse->process();
    
    expect($result)->toBe([]);
});

test('stream response can get underlying response', function () {
    $httpResponse = new Response(200);
    $streamResponse = new StreamResponse($httpResponse);
    
    expect($streamResponse->getResponse())->toBe($httpResponse);
});
