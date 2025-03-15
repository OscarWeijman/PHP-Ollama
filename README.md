# PHP Ollama

Een moderne PHP library voor het werken met Ollama, vanaf PHP 8.2.

## Installatie

```bash
composer require oscarweijman/php-ollama
```

## Gebruik

### Basis gebruik

```php
<?php

require 'vendor/autoload.php';

use OscarWeijman\PhpOllama\Ollama;

// Maak een nieuwe Ollama instantie
$ollama = new Ollama();

// Genereer een antwoord
$result = $ollama->generate('llama2', 'Wat is kunstmatige intelligentie?');
echo $result['response'];

// Chat met een model
$messages = [
    ['role' => 'user', 'content' => 'Hallo, wie ben jij?']
];
$result = $ollama->chat('llama2', $messages);
echo $result['message']['content'];
```

### Configuratie

```php
<?php

use OscarWeijman\PhpOllama\Config;
use OscarWeijman\PhpOllama\Ollama;

// Aangepaste configuratie
$config = new Config(
    baseUrl: 'http://localhost:11434', // Standaard
    timeout: 60 // Standaard is 30 seconden
);

$ollama = new Ollama($config);
```

### Werken met modellen

```php
<?php

use OscarWeijman\PhpOllama\Ollama;

$ollama = new Ollama();

// Lijst van beschikbare modellen
$models = $ollama->listModels();
foreach ($models as $model) {
    echo $model['name'] . "\n";
}

// Werken met een specifiek model
$model = $ollama->model('llama2');

// Genereer een antwoord met het model
$result = $model->generate('Wat is het weer vandaag?');
echo $result->getData()['response'];

// Chat met het model
$messages = [
    ['role' => 'user', 'content' => 'Hallo, wie ben jij?']
];
$result = $model->chat($messages);
echo $result->getData()['message']['content'];

// Informatie over het model
$info = $model->getInfo();
print_r($info);
```

### Geavanceerde opties

```php
<?php

use OscarWeijman\PhpOllama\Ollama;

$ollama = new Ollama();

// Genereer met extra opties
$result = $ollama->generate('llama2', 'Schrijf een kort verhaal', [
    'temperature' => 0.7,
    'top_p' => 0.9,
    'max_tokens' => 500,
]);

// Chat met extra opties
$messages = [
    ['role' => 'system', 'content' => 'Je bent een behulpzame assistent.'],
    ['role' => 'user', 'content' => 'Wat kan je voor me doen?']
];
$result = $ollama->chat('llama2', $messages, [
    'temperature' => 0.8,
    'top_k' => 40,
]);
```

### Streaming

```php
<?php

use GuzzleHttp\Client;

// Schakel output buffering uit
ob_implicit_flush(true);
if (ob_get_level()) ob_end_flush();

// Kies een model
$modelName = 'llama3';

// Maak een HTTP client
$client = new Client([
    'base_uri' => 'http://localhost:11434',
    'timeout' => 60,
]);

// Genereer met streaming
$response = $client->post('/api/generate', [
    'json' => [
        'model' => $modelName,
        'prompt' => 'Schrijf een kort verhaal.',
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

// Chat met streaming werkt op dezelfde manier, maar met het '/api/chat' endpoint
```

> **Opmerking**: De bovenstaande directe implementatie met Guzzle wordt aanbevolen voor streaming, omdat deze betrouwbaarder is dan de callback-methode.

### Embeddings

```php
<?php

use OscarWeijman\PhpOllama\Ollama;
use OscarWeijman\PhpOllama\Embeddings\Embeddings;

$ollama = new Ollama();

// Genereer embeddings voor een tekst
$result = $ollama->embeddings('llama2', 'Dit is een voorbeeldtekst');
$embedding = $result['embedding'];

// Gebruik de Embeddings class voor een specifiek model
$model = $ollama->model('llama2');
$embeddingsHandler = $model->embeddings();

// Genereer embeddings voor meerdere teksten
$batchResults = $embeddingsHandler->embedBatch([
    "Dit is de eerste tekst.",
    "Dit is de tweede tekst."
]);

// Bereken similarity tussen embeddings
$similarity = Embeddings::cosineSimilarity($batchResults[0]['embedding'], $batchResults[1]['embedding']);
echo "Similarity: $similarity\n";

// Bereken afstand tussen embeddings
$distance = Embeddings::euclideanDistance($batchResults[0]['embedding'], $batchResults[1]['embedding']);
echo "Distance: $distance\n";
```

## Licentie

MIT
