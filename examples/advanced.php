<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OscarWeijman\PhpOllama\Ollama;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;

// Maak een nieuwe Ollama instantie
$ollama = new Ollama();

try {
    // Kies een model
    $modelName = 'llama3.2'; // Pas dit aan naar een model dat je lokaal hebt geïnstalleerd
    $model = $ollama->model($modelName);

    // Toon model informatie
    echo "Model informatie voor {$modelName}:\n";
    $info = $model->getInfo();
    echo "- Model formaat: " . ($info['modelfile'] ?? 'Onbekend') . "\n";
    echo "- Parameters: " . ($info['parameters'] ?? 'Onbekend') . "\n\n";

    // Genereer met geavanceerde opties
    echo "Genereren met aangepaste parameters:\n";
    $result = $model->generate(
        "Schrijf een kort verhaal over een robot die leert programmeren.",
        [
            'temperature' => 0.7,
            'top_p' => 0.9,
            'max_tokens' => 200,
        ]
    );
    echo "Verhaal: " . $result->getData()['response'] . "\n\n";

    // Chat met systeem instructie
    echo "Chat met systeem instructie:\n";
    $messages = [
        ['role' => 'system', 'content' => 'Je bent een behulpzame programmeer-assistent die code uitlegt in eenvoudige taal.'],
        ['role' => 'user', 'content' => 'Wat is het verschil tussen een array en een object in PHP?']
    ];
    $result = $model->chat(
        $messages,
        [
            'temperature' => 0.5,
            'top_k' => 40,
        ]
    );
    echo "Antwoord: " . $result->getData()['message']['content'] . "\n";
} catch (OllamaException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
