<?php

declare(strict_types=1);

namespace MyApp\UriParser\Tests;

use MyApp\UriParser\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * ホストのみのURLをテスト
     */
    public function testUrlHost(): void
    {
        $url = 'http://example.com';
        $parsedUrl = (new Url($url))->parse();

        $this->assertSame('example.com', $parsedUrl->host);
        $this->assertSame('80', $parsedUrl->port);
        $this->assertSame('', $parsedUrl->path);
        $this->assertSame('', $parsedUrl->searchpart);
    }

    /**
     * ホストとポートのURLをテスト
     */
    public function testUrlHostPort(): void
    {
        $url = 'http://example.com:8888';
        $parsedUrl = (new Url($url))->parse();

        $this->assertSame('example.com', $parsedUrl->host);
        $this->assertSame('8888', $parsedUrl->port);
        $this->assertSame('', $parsedUrl->path);
        $this->assertSame('', $parsedUrl->searchpart);
    }

    /**
     * ホスト、ポート、パスのURLをテスト
     */
    public function testUrlHostPortPath(): void
    {
        $url = 'http://example.com:8888/index.html';
        $parsedUrl = (new Url($url))->parse();

        $this->assertSame('example.com', $parsedUrl->host);
        $this->assertSame('8888', $parsedUrl->port);
        $this->assertSame('index.html', $parsedUrl->path);
        $this->assertSame('', $parsedUrl->searchpart);
    }

    /**
     * ホストとパスのURLをテスト
     */
    public function testUrlHostPath(): void
    {
        $url = 'http://example.com/index.html';
        $parsedUrl = (new Url($url))->parse();

        $this->assertSame('example.com', $parsedUrl->host);
        $this->assertSame('80', $parsedUrl->port);
        $this->assertSame('index.html', $parsedUrl->path);
        $this->assertSame('', $parsedUrl->searchpart);
    }

    /**
     * ホスト、ポート、パス、検索パラメータのURLをテスト
     */
    public function testUrlHostPortPathSearchpart(): void
    {
        $url = 'http://example.com:8888/index.html?a=123&b=456';
        $parsedUrl = (new Url($url))->parse();

        $this->assertSame('example.com', $parsedUrl->host);
        $this->assertSame('8888', $parsedUrl->port);
        $this->assertSame('index.html', $parsedUrl->path);
        $this->assertSame('a=123&b=456', $parsedUrl->searchpart);
    }

    /**
     * スキームなしのURLでの例外テスト
     */
    public function testNoScheme(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only HTTP scheme is supported.');

        $url = 'example.com';
        (new Url($url))->parse();
    }

    /**
     * サポートされていないスキームでの例外テスト
     */
    public function testUnsupportedScheme(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only HTTP scheme is supported.');

        $url = 'https://example.com:8888/index.html';
        (new Url($url))->parse();
    }
}