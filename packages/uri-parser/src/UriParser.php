<?php

namespace MyApp\UriParser;

class UriParser
{
    public function parse(string $uri): array
    {
        // A simple URI parser, can be replaced with a more robust library if needed
        $parts = parse_url($uri);
        if ($parts === false) {
            throw new \InvalidArgumentException("Invalid URI: " . $uri);
        }
        return $parts;
    }
}
