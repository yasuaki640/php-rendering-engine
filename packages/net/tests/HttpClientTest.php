<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Net\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Exception\NetworkException;
use Yasuaki640\PhpRenderingEngine\Core\HttpResponse;

class HttpClientTest extends TestCase
{
    public function testInvalidHttpResponse(): void
    {
        $raw = "HTTP/1.1 200 OK";

        $this->expectException(NetworkException::class);
        new HttpResponse($raw);
    }

    public function testStatusLineOnly(): void
    {
        $raw = "HTTP/1.1 200 OK\n\n";
        $res = new HttpResponse($raw);

        $this->assertEquals("HTTP/1.1", $res->version);
        $this->assertEquals(200, $res->statusCode);
        $this->assertEquals("OK", $res->reason);
    }

    public function testOneHeader(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate:xx xx xx\n\n";
        $res = new HttpResponse($raw);

        $this->assertEquals("HTTP/1.1", $res->version);
        $this->assertEquals(200, $res->statusCode);
        $this->assertEquals("OK", $res->reason);
        $this->assertEquals("xx xx xx", $res->getHeaderValue("Date"));
    }

    public function testTwoHeadersWithWhiteSpace(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate: xx xx xx\nContent-Length: 42\n\n";
        $res = new HttpResponse($raw);

        $this->assertEquals("HTTP/1.1", $res->version);
        $this->assertEquals(200, $res->statusCode);
        $this->assertEquals("OK", $res->reason);
        $this->assertEquals("xx xx xx", $res->getHeaderValue("Date"));
        $this->assertEquals("42", $res->getHeaderValue("Content-Length"));
    }

    public function testBody(): void
    {
        $raw = "HTTP/1.1 200 OK\nDate: xx xx xx\n\nbody message";
        $res = new HttpResponse($raw);

        $this->assertEquals("HTTP/1.1", $res->version);
        $this->assertEquals(200, $res->statusCode);
        $this->assertEquals("OK", $res->reason);
        $this->assertEquals("xx xx xx", $res->getHeaderValue("Date"));
        $this->assertEquals("body message", $res->body);
    }
}
