<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Error;
use Yasuaki640\PhpRenderingEngine\Core\ErrorWithMessage;

class ErrorTest extends TestCase
{
    public function testErrorWithMessage(): void
    {
        $error = Error::Network->withMessage('Connection failed');

        $this->assertInstanceOf(ErrorWithMessage::class, $error);
        $this->assertEquals(Error::Network, $error->error);
        $this->assertEquals('Connection failed', $error->message);
    }

    public function testErrorToString(): void
    {
        $networkError = Error::Network->withMessage('Connection failed');
        $this->assertEquals('Network(Connection failed)', (string) $networkError);

        $unexpectedInputError = Error::UnexpectedInput->withMessage('Invalid token');
        $this->assertEquals('UnexpectedInput(Invalid token)', (string) $unexpectedInputError);

        $invalidUIError = Error::InvalidUI->withMessage('Widget not found');
        $this->assertEquals('InvalidUI(Widget not found)', (string) $invalidUIError);

        $otherError = Error::Other->withMessage('Unknown error');
        $this->assertEquals('Other(Unknown error)', (string) $otherError);
    }

    public function testAllErrorTypes(): void
    {
        $errors = [
            Error::Network,
            Error::UnexpectedInput,
            Error::InvalidUI,
            Error::Other,
        ];

        foreach ($errors as $error) {
            $errorWithMessage = $error->withMessage('test');
            $this->assertInstanceOf(ErrorWithMessage::class, $errorWithMessage);
        }
    }
}
