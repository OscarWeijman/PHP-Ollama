<?php

use OscarWeijman\PhpOllama\Client;
use OscarWeijman\PhpOllama\Config;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

// Helper function to create a mocked client
function createMockedClient(array $responses): Client
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);
    $httpClient = new HttpClient(['handler' => $handlerStack]);
    
    $config = new Config();
    $client = new Client($config);
    
    // Use reflection to replace the httpClient property
    $reflection = new ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);
    
    return $client;
}

test('client can generate completions', function () {
    $responseData = [
        'model' => 'llama2',
        'response' => 'This is a test response',
        'done' => true
    ];
    
    $client = createMockedClient([
        new Response(200, [], json_encode($responseData))
    ]);
    
    $response = $client->generate('llama2', 'Test prompt');
    $data = $response->getData();
    
    expect($data)->toBe($responseData);
    expect($data['response'])->toBe('This is a test response');
});

test('client can handle chat completions', function () {
    $responseData = [
        'model' => 'llama2',
        'message' => [
            'role' => 'assistant',
            'content' => 'This is a chat response'
        ],
        'done' => true
    ];
    
    $client = createMockedClient([
        new Response(200, [], json_encode($responseData))
    ]);
    
    $messages = [
        ['role' => 'user', 'content' => 'Hello']
    ];
    
    $response = $client->chat('llama2', $messages);
    $data = $response->getData();
    
    expect($data)->toBe($responseData);
    expect($data['message']['content'])->toBe('This is a chat response');
});

test('client can list models', function () {
    $responseData = [
        'models' => [
            ['name' => 'llama2'],
            ['name' => 'mistral']
        ]
    ];
    
    $client = createMockedClient([
        new Response(200, [], json_encode($responseData))
    ]);
    
    $response = $client->listModels();
    $data = $response->getData();
    
    expect($data)->toBe($responseData);
    expect($data['models'])->toHaveCount(2);
    expect($data['models'][0]['name'])->toBe('llama2');
});

test('client can get model information', function () {
    $responseData = [
        'name' => 'llama2',
        'modelfile' => 'FROM llama2...',
        'parameters' => 'Some parameters',
        'template' => 'Some template'
    ];
    
    $client = createMockedClient([
        new Response(200, [], json_encode($responseData))
    ]);
    
    $response = $client->getModel('llama2');
    $data = $response->getData();
    
    expect($data)->toBe($responseData);
    expect($data['name'])->toBe('llama2');
});

test('client throws exception on request error', function () {
    $client = createMockedClient([
        new RequestException('Error communicating with server', new Request('GET', 'test'))
    ]);
    
    expect(fn() => $client->listModels())->toThrow(OllamaException::class);
});
