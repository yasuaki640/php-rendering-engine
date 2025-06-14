<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

use Yasuaki640\PhpRenderingEngine\Core\Exception\NetworkException;

class HttpResponse
{
    public readonly string $version;
    public readonly int $statusCode;
    public readonly string $reason;
    /**
     * @var Header[]
     */
    public readonly array $headers;
    public readonly string $body;

    /**
     * @param Header[] $headers
     */
    public function __construct(
        public readonly string $rawResponse,
    ) {
        $trimed = ltrim($rawResponse);
        $preprocessedResponse = str_replace("\r\n", "\n", $trimed);

        $firstNewlinePos = strpos($preprocessedResponse, "\n");
        if ($firstNewlinePos === false) {
            throw new NetworkException(
                "invalid http response: " . $preprocessedResponse
            );
        }

        $statusLine = substr($preprocessedResponse, 0, $firstNewlinePos);
        $remaining = substr($preprocessedResponse, $firstNewlinePos + 1);

        // ヘッダとボディを分割
        $parts = explode("\n\n", $remaining, 2);
        if (count($parts) === 2) {
            [$headerSection, $body] = $parts;
            $headers = [];
            foreach (explode("\n", $headerSection) as $header) {
                if (trim($header) === '') {
                    continue; // 空行をスキップ
                }
                $splittedHeader = explode(':', $header, 2);
                if (count($splittedHeader) === 2) {
                    $headers[] = new Header(
                        trim($splittedHeader[0]),
                        trim($splittedHeader[1])
                    );
                }
            }
        } else {
            // ヘッダーとボディの分割ができない場合
            $headers = [];
            // remainingが空行のみの場合はボディを空にする
            $body = trim($remaining) === '' ? '' : $remaining;
        }

        $statuses = explode(' ', $statusLine);

        $this->version = $statuses[0] ?? '';
        $this->statusCode = isset($statuses[1]) ? (int) $statuses[1] : 404;
        $this->reason = $statuses[2] ?? '';
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * ヘッダー値を取得する
     */
    public function getHeaderValue(string $name): ?string
    {
        foreach ($this->headers as $header) {
            if (strcasecmp($header->name, $name) === 0) {
                return $header->value;
            }
        }

        return null;
    }
}
