<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama\Embeddings;

use OscarWeijman\PhpOllama\Client;
use OscarWeijman\PhpOllama\Exceptions\OllamaException;
use OscarWeijman\PhpOllama\Response\Response;

class Embeddings
{
    private Client $client;
    private string $model;

    /**
     * Create a new Embeddings instance
     *
     * @param Client $client
     * @param string $model
     */
    public function __construct(Client $client, string $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    /**
     * Generate embeddings for a single text
     *
     * @param string $text
     * @param array $options
     * @return array
     * @throws OllamaException
     */
    public function embed(string $text, array $options = []): array
    {
        $response = $this->client->embeddings($this->model, $text, $options);
        return $response->getData();
    }

    /**
     * Generate embeddings for multiple texts
     *
     * @param array $texts
     * @param array $options
     * @return array
     * @throws OllamaException
     */
    public function embedBatch(array $texts, array $options = []): array
    {
        $results = [];
        
        foreach ($texts as $text) {
            $results[] = $this->embed($text, $options);
        }
        
        return $results;
    }

    /**
     * Calculate cosine similarity between two embeddings
     *
     * @param array $embedding1
     * @param array $embedding2
     * @return float
     */
    public static function cosineSimilarity(array $embedding1, array $embedding2): float
    {
        if (empty($embedding1) || empty($embedding2) || count($embedding1) !== count($embedding2)) {
            throw new \InvalidArgumentException('Embeddings must be non-empty and have the same dimensions');
        }

        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;

        foreach ($embedding1 as $i => $value) {
            $dotProduct += $value * $embedding2[$i];
            $norm1 += $value * $value;
            $norm2 += $embedding2[$i] * $embedding2[$i];
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }

        return $dotProduct / ($norm1 * $norm2);
    }

    /**
     * Calculate Euclidean distance between two embeddings
     *
     * @param array $embedding1
     * @param array $embedding2
     * @return float
     */
    public static function euclideanDistance(array $embedding1, array $embedding2): float
    {
        if (empty($embedding1) || empty($embedding2) || count($embedding1) !== count($embedding2)) {
            throw new \InvalidArgumentException('Embeddings must be non-empty and have the same dimensions');
        }

        $sum = 0;
        foreach ($embedding1 as $i => $value) {
            $diff = $value - $embedding2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }
}
