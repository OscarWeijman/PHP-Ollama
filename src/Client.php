<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;
use OscarWeijman\PhpOllama\Config;
use OscarWeijman\PhpOllama\Response\Response;
use OscarWeijman\PhpOllama\Response\StreamResponse;

class Client
{
    private HttpClient $httpClient;
    private Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? new Config();
        $this->httpClient = new HttpClient([
            'base_uri' => $this->config->getBaseUrl(),
            'timeout' => $this->config->getTimeout(),
        ]);
    }

    /**
     * Generate a completion from a prompt
     *
     * @param string $model The name of the model to use
     * @param string $prompt The prompt to generate from
     * @param array $options Additional options for the generation
     * @param bool $stream Whether to stream the response
     * @param callable|null $streamCallback Callback function for streaming
     * @return Response|StreamResponse
     * @throws OllamaException
     */
    public function generate(string $model, string $prompt, array $options = [], bool $stream = false, ?callable $streamCallback = null)
    {
        $payload = array_merge([
            'model' => $model,
            'prompt' => $prompt,
            'stream' => $stream,
        ], $options);

        if ($stream) {
            return $this->postStream('/api/generate', $payload, $streamCallback);
        }

        return $this->post('/api/generate', $payload);
    }

    /**
     * Generate a chat completion
     *
     * @param string $model The name of the model to use
     * @param array $messages The messages to generate from
     * @param array $options Additional options for the generation
     * @param bool $stream Whether to stream the response
     * @param callable|null $streamCallback Callback function for streaming
     * @return Response|StreamResponse
     * @throws OllamaException
     */
    public function chat(string $model, array $messages, array $options = [], bool $stream = false, ?callable $streamCallback = null)
    {
        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'stream' => $stream,
        ], $options);

        if ($stream) {
            return $this->postStream('/api/chat', $payload, $streamCallback);
        }

        return $this->post('/api/chat', $payload);
    }

    /**
     * List available models
     *
     * @return Response
     * @throws OllamaException
     */
    public function listModels(): Response
    {
        return $this->get('/api/tags');
    }

    /**
     * Get model information
     *
     * @param string $model The name of the model
     * @return Response
     * @throws OllamaException
     */
    public function getModel(string $model): Response
    {
        return $this->post('/api/show', ['name' => $model]);
    }
    
    /**
     * Generate embeddings for a text
     *
     * @param string $model The name of the model to use
     * @param string $text The text to generate embeddings for
     * @param array $options Additional options for the generation
     * @return Response
     * @throws OllamaException
     */
    public function embeddings(string $model, string $text, array $options = []): Response
    {
        $payload = array_merge([
            'model' => $model,
            'prompt' => $text,
        ], $options);

        return $this->post('/api/embeddings', $payload);
    }

    /**
     * Send a GET request to the Ollama API
     *
     * @param string $endpoint
     * @return Response
     * @throws OllamaException
     */
    private function get(string $endpoint): Response
    {
        try {
            $response = $this->httpClient->get($endpoint);
            return new Response($response);
        } catch (GuzzleException $e) {
            throw new OllamaException('Failed to send GET request: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Send a POST request to the Ollama API
     *
     * @param string $endpoint
     * @param array $data
     * @return Response
     * @throws OllamaException
     */
    private function post(string $endpoint, array $data): Response
    {
        try {
            $response = $this->httpClient->post($endpoint, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
            return new Response($response);
        } catch (GuzzleException $e) {
            throw new OllamaException('Failed to send POST request: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Send a streaming POST request to the Ollama API
     *
     * @param string $endpoint
     * @param array $data
     * @param callable|null $callback
     * @return StreamResponse
     * @throws OllamaException
     */
    private function postStream(string $endpoint, array $data, ?callable $callback = null): StreamResponse
    {
        try {
            $response = $this->httpClient->post($endpoint, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'stream' => true,
            ]);
            return new StreamResponse($response, $callback);
        } catch (GuzzleException $e) {
            throw new OllamaException('Failed to send streaming POST request: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
