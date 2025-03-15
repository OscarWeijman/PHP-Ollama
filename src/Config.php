<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama;

class Config
{
    private string $baseUrl;
    private int $timeout;

    /**
     * Create a new Config instance
     *
     * @param string $baseUrl The base URL for the Ollama API
     * @param int $timeout Request timeout in seconds
     */
    public function __construct(string $baseUrl = 'http://localhost:11434', int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Set the base URL
     *
     * @param string $baseUrl
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get the timeout
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Set the timeout
     *
     * @param int $timeout
     * @return self
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
}
