<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama\Response;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private int $statusCode;
    private array $data;
    private string $rawBody;

    /**
     * Create a new Response instance
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->statusCode = $response->getStatusCode();
        $this->rawBody = (string) $response->getBody();
        $this->data = json_decode($this->rawBody, true) ?? [];
    }

    /**
     * Get the status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the response data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the raw response body
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    /**
     * Check if the response was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Get a specific value from the response data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
