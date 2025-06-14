<?php

declare(strict_types=1);

namespace MyApp\Core;

/**
 * Unified error enum corresponding to error.rs in the Rust implementation.
 */
enum Error
{
    case Network;
    case UnexpectedInput;
    case InvalidUI;
    case Other;

    /**
     * Create an Error with a message.
     */
    public function withMessage(string $message): ErrorWithMessage
    {
        return new ErrorWithMessage($this, $message);
    }
}

/**
 * Error wrapper that includes a message.
 */
class ErrorWithMessage
{
    public function __construct(
        public readonly Error $error,
        public readonly string $message
    ) {}

    public function __toString(): string
    {
        return match ($this->error) {
            Error::Network => "Network({$this->message})",
            Error::UnexpectedInput => "UnexpectedInput({$this->message})",
            Error::InvalidUI => "InvalidUI({$this->message})",
            Error::Other => "Other({$this->message})",
        };
    }
}
