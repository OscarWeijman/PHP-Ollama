<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OscarWeijman\PhpOllama\Ollama;
use OscarWeijman\PhpOllama\Config;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;

// Basis configuratie
$config = new Config(
    baseUrl: 'http://localhost:11434',
    timeout: 60
);

// Maak een nieuwe Ollama instantie
$ollama = new Ollama($config);

try {
    // Lijst van beschikbare modellen
    echo "Beschikbare modellen:\n";
    $models = $ollama->listModels();
    foreach ($models as $model) {
        echo "- {$model['name']}\n";
    }
    echo "\n";

    // Kies een model (bijvoorbeeld llama2)
    $modelName = 'mistral'; // Pas dit aan naar een model dat je lokaal hebt geïnstalleerd

    // Genereer een antwoord
    echo "Genereren van een antwoord met {$modelName}:\n";
    $result = $ollama->generate($modelName, 'Wat is kunstmatige intelligentie?');
    echo "Antwoord: {$result['response']}\n\n";

    // Chat met een model
    echo "Chat met {$modelName}:\n";
    $messages = [
        ['role' => 'user', 'content' => 'Hallo, wie ben jij?']
    ];
    $result = $ollama->chat($modelName, $messages);
    echo "Antwoord: {$result['message']['content']}\n";
} catch (OllamaException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
