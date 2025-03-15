<?php

use OscarWeijman\PhpOllama\Ollama;
use OscarWeijman\PhpOllama\Client;
use OscarWeijman\PhpOllama\Config;
use OscarWeijman\PhpOllama\Model;
use OscarWeijman\PhpOllama\Response\Response;
use OscarWeijman\PhpOllama\Response\StreamResponse;

test('ollama can be instantiated with default config', function () {
    $ollama = new Ollama();
    expect($ollama)->toBeInstanceOf(Ollama::class);
    expect($ollama->getClient())->toBeInstanceOf(Client::class);
});

test('ollama can be instantiated with custom config', function () {
    $config = new Config('https://custom.example.com', 60);
    $ollama = new Ollama($config);
    expect($ollama)->toBeInstanceOf(Ollama::class);
});

test('ollama can create model instances', function () {
    $ollama = new Ollama();
    $model = $ollama->model('llama2');
    
    expect($model)->toBeInstanceOf(Model::class);
    expect($model->getName())->toBe('llama2');
});

test('ollama can list models', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('get')
        ->with('models', [])
        ->andReturn([
            ['name' => 'llama2'],
            ['name' => 'mistral']
        ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('listModels')->andReturn($mockResponse);
    
    $ollama = new Ollama();
    
    // Use reflection to replace the client property
    $reflection = new ReflectionClass($ollama);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($ollama, $client);
    
    $models = $ollama->listModels();
    
    expect($models)->toBe([
        ['name' => 'llama2'],
        ['name' => 'mistral']
    ]);
});

test('ollama can generate completions', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')
        ->andReturn([
            'model' => 'llama2',
            'response' => 'This is a test response',
            'done' => true
        ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('generate')
        ->with('llama2', 'Test prompt', ['temperature' => 0.7], false, null)
        ->andReturn($mockResponse);
    
    $ollama = new Ollama();
    
    // Use reflection to replace the client property
    $reflection = new ReflectionClass($ollama);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($ollama, $client);
    
    $result = $ollama->generate('llama2', 'Test prompt', ['temperature' => 0.7]);
    
    expect($result)->toBe([
        'model' => 'llama2',
        'response' => 'This is a test response',
        'done' => true
    ]);
});

test('ollama can generate streaming completions', function () {
    $mockStreamResponse = Mockery::mock(StreamResponse::class);
    $mockStreamResponse->shouldReceive('process')
        ->andReturn([
            'model' => 'llama2',
            'response' => 'This is a test response',
            'done' => true,
            'full_response' => 'This is a test response'
        ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('generate')
        ->with('llama2', 'Test prompt', [], true, Mockery::type('callable'))
        ->andReturn($mockStreamResponse);
    
    $ollama = new Ollama();
    
    // Use reflection to replace the client property
    $reflection = new ReflectionClass($ollama);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($ollama, $client);
    
    $callback = function ($chunk) {};
    $result = $ollama->generate('llama2', 'Test prompt', [], true, $callback);
    
    expect($result)->toBe([
        'model' => 'llama2',
        'response' => 'This is a test response',
        'done' => true,
        'full_response' => 'This is a test response'
    ]);
});

test('ollama can chat', function () {
    $mockResponse = Mockery::mock(Response::class);
    $mockResponse->shouldReceive('getData')
        ->andReturn([
            'model' => 'llama2',
            'message' => [
                'role' => 'assistant',
                'content' => 'This is a chat response'
            ],
            'done' => true
        ]);
    
    $client = Mockery::mock(Client::class);
    $client->shouldReceive('chat')
        ->with('llama2', [['role' => 'user', 'content' => 'Hello']], [], false, null)
        ->andReturn($mockResponse);
    
    $ollama = new Ollama();
    
    // Use reflection to replace the client property
    $reflection = new ReflectionClass($ollama);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($ollama, $client);
    
    $result = $ollama->chat('llama2', [['role' => 'user', 'content' => 'Hello']]);
    
    expect($result)->toBe([
        'model' => 'llama2',
        'message' => [
            'role' => 'assistant',
            'content' => 'This is a chat response'
        ],
        'done' => true
    ]);
});
