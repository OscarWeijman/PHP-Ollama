<?php

use OscarWeijman\PhpOllama\Response\Response;

test('response can parse json data', function () {
    $httpResponse = mockResponse(200, ['key' => 'value']);
    $response = new Response($httpResponse);
    
    expect($response->getData())->toBe(['key' => 'value']);
    expect($response->get('key'))->toBe('value');
    expect($response->getStatusCode())->toBe(200);
    expect($response->isSuccessful())->toBeTrue();
});

test('response handles empty data', function () {
    $httpResponse = mockResponse(204, []);
    $response = new Response($httpResponse);
    
    expect($response->getData())->toBe([]);
    expect($response->get('nonexistent'))->toBeNull();
    expect($response->get('nonexistent', 'default'))->toBe('default');
    expect($response->isSuccessful())->toBeTrue();
});

test('response handles non-successful status codes', function () {
    $httpResponse = mockResponse(404, ['error' => 'Not found']);
    $response = new Response($httpResponse);
    
    expect($response->getStatusCode())->toBe(404);
    expect($response->isSuccessful())->toBeFalse();
    expect($response->get('error'))->toBe('Not found');
});

test('response can get raw body', function () {
    $body = '{"custom":"format"}';
    $httpResponse = mockResponse(200, [], $body);
    $response = new Response($httpResponse);
    
    expect($response->getRawBody())->toBe($body);
});
