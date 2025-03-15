<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama;

use OscarWeijman\PhpOllama\Exceptions\OllamaException;

class Ollama
{
    private Client $client;

    /**
     * Create a new Ollama instance
     *
     * @param Config|null $config
     */
    public function __construct(?Config $config = null)
    {
        $this->client = new Client($config);
    }

    /**
     * Get the client instance
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Create a new model instance
     *
     * @param string $name
     * @return Model
     */
    public function model(string $name): Model
    {
        return new Model($this->client, $name);
    }

    /**
     * List all available models
     *
     * @return array
     * @throws OllamaException
     */
    public function listModels(): array
    {
        $response = $this->client->listModels();
        return $response->get('models', []);
    }

    /**
     * Generate a completion from a prompt
     *
     * @param string $model
     * @param string $prompt
     * @param array $options
     * @param bool $stream
     * @param callable|null $streamCallback
     * @return array
     * @throws OllamaException
     */
    public function generate(string $model, string $prompt, array $options = [], bool $stream = false, ?callable $streamCallback = null): array
    {
        $response = $this->client->generate($model, $prompt, $options, $stream, $streamCallback);
        
        if ($stream) {
            return $response->process();
        }
        
        return $response->getData();
    }

    /**
     * Generate a chat completion
     *
     * @param string $model
     * @param array $messages
     * @param array $options
     * @param bool $stream
     * @param callable|null $streamCallback
     * @return array
     * @throws OllamaException
     */
    public function chat(string $model, array $messages, array $options = [], bool $stream = false, ?callable $streamCallback = null): array
    {
        $response = $this->client->chat($model, $messages, $options, $stream, $streamCallback);
        
        if ($stream) {
            return $response->process();
        }
        
        return $response->getData();
    }
    
    /**
     * Generate embeddings for a text
     *
     * @param string $model
     * @param string $text
     * @param array $options
     * @return array
     * @throws OllamaException
     */
    public function embeddings(string $model, string $text, array $options = []): array
    {
        $response = $this->client->embeddings($model, $text, $options);
        return $response->getData();
    }
}
