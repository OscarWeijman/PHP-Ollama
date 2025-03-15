<?php

use OscarWeijman\PhpOllama\Config;

test('config has default values', function () {
    $config = new Config();
    
    expect($config->getBaseUrl())->toBe('http://localhost:11434');
    expect($config->getTimeout())->toBe(30);
});

test('config can be initialized with custom values', function () {
    $config = new Config('https://custom-ollama.example.com', 60);
    
    expect($config->getBaseUrl())->toBe('https://custom-ollama.example.com');
    expect($config->getTimeout())->toBe(60);
});

test('config can be modified after initialization', function () {
    $config = new Config();
    
    $config->setBaseUrl('https://modified-ollama.example.com');
    $config->setTimeout(120);
    
    expect($config->getBaseUrl())->toBe('https://modified-ollama.example.com');
    expect($config->getTimeout())->toBe(120);
});

test('config removes trailing slash from base url', function () {
    $config = new Config('https://ollama.example.com/');
    
    expect($config->getBaseUrl())->toBe('https://ollama.example.com');
});
