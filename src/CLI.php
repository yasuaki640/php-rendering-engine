<?php

namespace Yasuaki640\PhpRenderingEngine;

use MyApp\Core\HttpResponse;
use MyApp\Net\HttpClient;

class CLI
{
    public function run(): void
    {
        $args = $_SERVER['argv'] ?? [];

        if (count($args) > 1 && $args[1] === 'test-http') {
            $this->testHttpClient();
        } elseif (count($args) > 1 && $args[1] === 'test-host') {
            $this->testHostClient();
        } elseif (count($args) > 1 && $args[1] === 'test-example') {
            $this->testExampleCom();
        } else {
            echo "Available commands:\n";
            echo "  test-http     - Test HTTP client with httpbin.org\n";
            echo "  test-host     - Test HTTP client with host.test:8000\n";
            echo "  test-example  - Test HTTP client with example.com\n";
            echo "\nUsage: php bin/hello <command>\n";
        }
    }

    private function testHttpClient(): void
    {
        $client = new HttpClient();

        try {
            // 実際にアクセス可能なホストでテスト
            $response = $client->get("httpbin.org", 80, "get");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function testHostClient(): void
    {
        $client = new HttpClient();

        try {
            // Rustコードと同じパラメータでテスト: host.test:8000/test.html
            $response = $client->get("host.test", 8000, "test.html");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function testExampleCom(): void
    {
        $client = new HttpClient();

        try {
            // example.comでテスト（存在する実際のWebサイト）
            $response = $client->get("example.com", 80, "");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function printResponse(HttpResponse $response): void
    {
        echo "Version: " . $response->version . "\n";
        echo "Status Code: " . $response->statusCode . "\n";
        echo "Reason: " . $response->reason . "\n";
        echo "Headers:\n";

        foreach ($response->headers as $header) {
            echo "  " . $header->name . ": " . $header->value . "\n";
        }

        echo "Body:\n";
        echo $response->body . "\n";

        echo "\nRaw Response:\n";
        echo "============\n";
        echo $response->rawResponse . "\n";
    }
}
