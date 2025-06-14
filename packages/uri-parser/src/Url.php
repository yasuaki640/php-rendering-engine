<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\UriParser;

class Url
{
    private string $url;
    public readonly string $host;
    public readonly string $port;
    public readonly string $path;
    public readonly string $searchpart;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    private function isHttp(): bool
    {
        return str_contains($this->url, 'http://');
    }

    private function extractHost(): string
    {
        $urlParts = explode('/', ltrim($this->url, 'http://'), 2);

        $colonPosition = strpos($urlParts[0], ':');
        if ($colonPosition !== false) {
            return substr($urlParts[0], 0, $colonPosition);
        }

        return $urlParts[0];
    }

    private function extractPath(): string
    {
        $urlParts = explode('/', ltrim($this->url, 'http://'), 2);

        if (count($urlParts) < 2) {
            return '';
        }

        $pathAndSearchpart = explode('?', $urlParts[1], 2);

        return $pathAndSearchpart[0];
    }

    private function extractPort(): string
    {
        $urlParts = explode('/', ltrim($this->url, 'http://'), 2);

        $colonPosition = strpos($urlParts[0], ':');
        if ($colonPosition !== false) {
            return substr($urlParts[0], $colonPosition + 1);
        }

        return '80';
    }

    private function extractSearchpart(): string
    {
        $urlParts = explode('/', ltrim($this->url, 'http://'), 2);

        if (count($urlParts) < 2) {
            return '';
        }

        $pathAndSearchpart = explode('?', $urlParts[1], 2);
        if (count($pathAndSearchpart) < 2) {
            return '';
        }

        return $pathAndSearchpart[1];
    }

    /**
     * @throws \Exception
     */
    public function parse(): self
    {
        if (! $this->isHttp()) {
            throw new \Exception('Only HTTP scheme is supported.');
        }

        $this->host = $this->extractHost();
        $this->port = $this->extractPort();
        $this->path = $this->extractPath();
        $this->searchpart = $this->extractSearchpart();

        return $this;
    }
}
