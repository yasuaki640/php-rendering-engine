<?php

declare(strict_types=1);

namespace MyApp\Net;

use MyApp\Core\HttpResponse;

class HttpClient
{
    public function __construct() {}

    // TODO: implement a proper HTTP client
    public function get(string $host, int $port, string $path): HttpResponse
    {
        $ips = gethostbynamel($host);
        if ($ips === false) {
            throw new \Exception("Failed to find IP addresses");
        }

        if (count($ips) < 1) {
            throw new \Exception("Failed to find IP addresses");
        }

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new \Exception("Failed to create socket");
        }

        $result = socket_connect($socket, $ips[0], $port);
        if ($result === false) {
            socket_close($socket);

            throw new \Exception("Failed to connect to TCP stream");
        }

        // リクエストラインの作成
        $request = "GET /" . $path . " HTTP/1.1\r\n";

        // ヘッダの追加
        $request .= "Host: " . $host . "\r\n";
        $request .= "Accept: text/html\r\n";
        $request .= "Connection: close\r\n";
        $request .= "\r\n";

        $bytes_written = socket_write($socket, $request, strlen($request));
        if ($bytes_written === false) {
            socket_close($socket);

            throw new \Exception("Failed to send a request to TCP stream");
        }

        $received = '';
        while (true) {
            $buf = socket_read($socket, 4096);
            if ($buf === false) {
                socket_close($socket);

                throw new \Exception("Failed to receive a request from TCP stream");
            }
            if (strlen($buf) === 0) {
                break;
            }
            $received .= $buf;
        }

        socket_close($socket);

        if (! mb_check_encoding($received, 'UTF-8')) {
            throw new \Exception("Invalid received response: not valid UTF-8");
        }

        return new HttpResponse($received);
    }
}
