<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Css;

/**
 * Component Value representation based on CSS Syntax Level 3 specification
 * Equivalent to Rust's ComponentValue = CssToken
 * @see https://www.w3.org/TR/css-syntax-3/#component-value
 */
class ComponentValue extends CssToken
{
    // ComponentValue is essentially a CssToken in our implementation
    // This class exists for type clarity and future extensibility
}
