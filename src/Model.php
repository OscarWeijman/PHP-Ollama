<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama;

use OscarWeijman\PhpOllama\Exceptions\OllamaException;
use OscarWeijman\PhpOllama\Response\Response;
use OscarWeijman\PhpOllama\Response\StreamResponse;
use OscarWeijman\PhpOllama\Embeddings\Embeddings;

class Model
{
    private Client $client;
    private string $name;
    private ?array $modelInfo = null;

    /**
     * Create a new Model instance
     *
     * @param Client $client
     * @param string $name
     */
    public function __construct(Client $client, string $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * Get the model name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Generate a completion from a prompt
     *
     * @param string $prompt
     * @param array $options
     * @param bool $stream
     * @param callable|null $streamCallback
     * @return Response|StreamResponse
     * @throws OllamaException
     */
    public function generate(string $prompt, array $options = [], bool $stream = false, ?callable $streamCallback = null)
    {
        return $this->client->generate($this->name, $prompt, $options, $stream, $streamCallback);
    }

    /**
     * Generate a chat completion
     *
     * @param array $messages
     * @param array $options
     * @param bool $stream
     * @param callable|null $streamCallback
     * @return Response|StreamResponse
     * @throws OllamaException
     */
    public function chat(array $messages, array $options = [], bool $stream = false, ?callable $streamCallback = null)
    {
        return $this->client->chat($this->name, $messages, $options, $stream, $streamCallback);
    }

    /**
     * Get information about the model
     *
     * @return array
     * @throws OllamaException
     */
    public function getInfo(): array
    {
        if ($this->modelInfo === null) {
            $response = $this->client->getModel($this->name);
            $this->modelInfo = $response->getData();
        }

        return $this->modelInfo;
    }
    
    /**
     * Create an embeddings instance for this model
     *
     * @return Embeddings
     */
    public function embeddings(): Embeddings
    {
        return new Embeddings($this->client, $this->name);
    }
    
    /**
     * Generate embeddings for a text
     *
     * @param string $text
     * @param array $options
     * @return Response
     * @throws OllamaException
     */
    public function embed(string $text, array $options = []): Response
    {
        return $this->client->embeddings($this->name, $text, $options);
    }
}
