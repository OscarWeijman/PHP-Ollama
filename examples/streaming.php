<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

// Schakel output buffering uit
ob_implicit_flush(true);
if (ob_get_level()) ob_end_flush();

try {
    // Kies een model
    $modelName = 'llama3.2'; // Pas dit aan naar een model dat je lokaal hebt geïnstalleerd
    
    echo "Streaming generatie met {$modelName}:\n\n";
    
    // Maak een HTTP client
    $client = new Client([
        'base_uri' => 'http://localhost:11434',
        'timeout' => 60,
    ]);
    
    // Genereer met streaming
    $response = $client->post('/api/generate', [
        'json' => [
            'model' => $modelName,
            'prompt' => 'Schrijf een kort verhaal over een avontuur in Amsterdam.',
            'temperature' => 0.7,
            'stream' => true,
        ],
        'stream' => true,
    ]);
    
    // Verwerk de streaming response
    $body = $response->getBody();
    
    // Lees de stream regel voor regel
    while (!$body->eof()) {
        // Lees een regel
        $line = '';
        while (!$body->eof()) {
            $char = $body->read(1);
            if ($char === "\n") {
                break;
            }
            $line .= $char;
        }
        
        // Verwerk de regel als het niet leeg is
        if (!empty($line)) {
            $data = json_decode($line, true);
            if ($data && isset($data['response'])) {
                echo $data['response'];
                flush();
            }
        }
    }
    
    echo "\n\nGeneratie voltooid!\n";
    
    // Chat met streaming
    echo "\nStreaming chat met {$modelName}:\n\n";
    
    // Chat met streaming
    $response = $client->post('/api/chat', [
        'json' => [
            'model' => $modelName,
            'messages' => [
                ['role' => 'user', 'content' => 'Wat zijn de top 5 bezienswaardigheden in Nederland?']
            ],
            'temperature' => 0.7,
            'stream' => true,
        ],
        'stream' => true,
    ]);
    
    // Verwerk de streaming response
    $body = $response->getBody();
    
    // Lees de stream regel voor regel
    while (!$body->eof()) {
        // Lees een regel
        $line = '';
        while (!$body->eof()) {
            $char = $body->read(1);
            if ($char === "\n") {
                break;
            }
            $line .= $char;
        }
        
        // Verwerk de regel als het niet leeg is
        if (!empty($line)) {
            $data = json_decode($line, true);
            if ($data && isset($data['message']['content'])) {
                echo $data['message']['content'];
                flush();
            }
        }
    }
    
    echo "\n\nChat voltooid!\n";
    
} catch (GuzzleException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
