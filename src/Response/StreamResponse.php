<?php

declare(strict_types=1);

namespace OscarWeijman\PhpOllama\Response;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Stream;

class StreamResponse
{
    private ResponseInterface $response;
    private $stream;
    private $callback;

    /**
     * Create a new StreamResponse instance
     *
     * @param ResponseInterface $response
     * @param callable|null $callback Function to call for each chunk of data
     */
    public function __construct(ResponseInterface $response, ?callable $callback = null)
    {
        $this->response = $response;
        $this->stream = $response->getBody();
        $this->callback = $callback;
    }

    /**
     * Process the stream and call the callback for each chunk
     *
     * @return array The collected response data
     */
    public function process(): array
    {
        $fullResponse = '';
        $responseData = [];

        while (!$this->stream->eof()) {
            $line = $this->readLine();
            if (empty($line)) {
                continue;
            }

            $data = json_decode($line, true);
            if (!$data) {
                continue;
            }

            // Call the callback if provided
            if ($this->callback) {
                call_user_func($this->callback, $data);
            }

            // For text generation, collect the response
            if (isset($data['response'])) {
                $fullResponse .= $data['response'];
                flush(); // Zorg ervoor dat de output direct wordt getoond
            }

            // Store the last chunk as our final data
            $responseData = $data;
        }

        // For text generation, ensure we have the full text
        if (isset($responseData['response'])) {
            $responseData['full_response'] = $fullResponse;
        }

        return $responseData;
    }

    /**
     * Read a line from the stream
     *
     * @return string
     */
    private function readLine(): string
    {
        $buffer = '';
        while (!$this->stream->eof()) {
            $byte = $this->stream->read(1);
            if ($byte === "\n") {
                break;
            }
            $buffer .= $byte;
        }
        return trim($buffer);
    }

    /**
     * Get the underlying response
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
