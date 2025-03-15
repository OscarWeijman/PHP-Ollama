<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OscarWeijman\PhpOllama\Ollama;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;
use OscarWeijman\PhpOllama\Embeddings\Embeddings;

// Maak een nieuwe Ollama instantie
$ollama = new Ollama();

try {
    // Kies een model dat embeddings ondersteunt
    $modelName = 'nomic-embed-text'; // Pas dit aan naar een model dat je lokaal hebt geïnstalleerd

    echo "Genereren van embeddings met {$modelName}:\n";

    // Genereer embeddings voor een tekst
    $text = "Dit is een voorbeeldtekst voor embeddings.";
    $result = $ollama->embeddings($modelName, $text);

    // Toon de dimensies van de embedding
    $embedding = $result['embedding'];
    echo "Embedding dimensies: " . count($embedding) . "\n";
    echo "Eerste 5 waarden: " . implode(', ', array_slice($embedding, 0, 5)) . "...\n\n";

    // Genereer embeddings voor meerdere teksten
    echo "Vergelijken van embeddings voor verschillende teksten:\n";

    $texts = [
        "Amsterdam is de hoofdstad van Nederland.",
        "Rotterdam heeft de grootste haven van Europa.",
        "Amsterdam heeft veel grachten en musea."
    ];

    $model = $ollama->model($modelName);
    $embeddingsHandler = $model->embeddings();

    $embeddings = [];
    foreach ($texts as $i => $text) {
        $result = $embeddingsHandler->embed($text);
        $embeddings[$i] = $result['embedding'];
        echo "Tekst " . ($i + 1) . " geëmbedded.\n";
    }

    // Bereken cosine similarity tussen de embeddings
    echo "\nCosine similarity tussen teksten:\n";
    echo "Tekst 1 en Tekst 2: " . Embeddings::cosineSimilarity($embeddings[0], $embeddings[1]) . "\n";
    echo "Tekst 1 en Tekst 3: " . Embeddings::cosineSimilarity($embeddings[0], $embeddings[2]) . "\n";
    echo "Tekst 2 en Tekst 3: " . Embeddings::cosineSimilarity($embeddings[1], $embeddings[2]) . "\n";

    // Bereken Euclidean distance tussen de embeddings
    echo "\nEuclidean distance tussen teksten:\n";
    echo "Tekst 1 en Tekst 2: " . Embeddings::euclideanDistance($embeddings[0], $embeddings[1]) . "\n";
    echo "Tekst 1 en Tekst 3: " . Embeddings::euclideanDistance($embeddings[0], $embeddings[2]) . "\n";
    echo "Tekst 2 en Tekst 3: " . Embeddings::euclideanDistance($embeddings[1], $embeddings[2]) . "\n";

    // Demonstreer batch embeddings
    echo "\nBatch embeddings genereren:\n";
    $batchResults = $embeddingsHandler->embedBatch([
        "Dit is de eerste tekst.",
        "Dit is de tweede tekst.",
        "Dit is de derde tekst."
    ]);

    echo "Aantal batch resultaten: " . count($batchResults) . "\n";
} catch (OllamaException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
