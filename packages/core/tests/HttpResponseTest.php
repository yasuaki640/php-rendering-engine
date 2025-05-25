<?php

declare(strict_types=1);

namespace MyApp\Core\Tests;

use MyApp\Core\HttpResponse;
use MyApp\Core\Exception\NetworkException;
use PHPUnit\Framework\TestCase;

class HttpResponseTest extends TestCase
{
    public function testInvalid(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage("invalid http response: HTTP/1.1 200 OK");
        
        $raw = "HTTP/1.1 200 OK";
        new HttpResponse($raw);
    }

    public function testStatusLineOnly(): void
    {
        $raw = "HTTP/1.1 200 OK\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals("OK", $response->reason);
        $this->assertEmpty($response->headers);
        $this->assertEmpty($response->body);
    }

    public function testOneHeader(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate:xx xx xx\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals("OK", $response->reason);
        
        $this->assertEquals("xx xx xx", $response->getHeaderValue("Date"));
        $this->assertCount(1, $response->headers);
    }

    public function testTwoHeadersWithWhiteSpace(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate: xx xx xx\nContent-Length: 42\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals("OK", $response->reason);
        
        $this->assertEquals("xx xx xx", $response->getHeaderValue("Date"));
        $this->assertEquals("42", $response->getHeaderValue("Content-Length"));
        $this->assertCount(2, $response->headers);
    }

    public function testBody(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate: xx xx xx\n\nbody message";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals("OK", $response->reason);
        
        $this->assertEquals("xx xx xx", $response->getHeaderValue("Date"));
        $this->assertEquals("body message", $response->body);
        $this->assertCount(1, $response->headers);
    }

    public function testHeaderCaseInsensitive(): void
    {
        $raw = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n";
        $response = new HttpResponse($raw);
        
        // 大文字小文字を区別しないでヘッダーを取得できることを確認
        $this->assertEquals("text/html", $response->getHeaderValue("Content-Type"));
        $this->assertEquals("text/html", $response->getHeaderValue("content-type"));
        $this->assertEquals("text/html", $response->getHeaderValue("CONTENT-TYPE"));
    }

    public function testNonExistentHeader(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate: xx xx xx\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertNull($response->getHeaderValue("NonExistent"));
    }

    public function testMultipleSpacesInStatusLine(): void
    {
        $raw = "HTTP/1.1 404 Not Found\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(404, $response->statusCode);
        $this->assertEquals("Not", $response->reason); // スペースで分割されるため
    }

    public function testCarriageReturnNewlineHandling(): void
    {
        $raw = "HTTP/1.1 200 OK\r\nDate: xx xx xx\r\n\r\nbody message";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("HTTP/1.1", $response->version);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals("OK", $response->reason);
        $this->assertEquals("xx xx xx", $response->getHeaderValue("Date"));
        $this->assertEquals("body message", $response->body);
    }

    public function testEmptyHeaderValue(): void
    {
        $raw = "HTTP/1.1 200 OK\nEmpty-Header:\n\n";
        $response = new HttpResponse($raw);
        
        $this->assertEquals("", $response->getHeaderValue("Empty-Header"));
    }
}
