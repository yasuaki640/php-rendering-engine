<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

/**
 * HTMLトークナイザーの状態を表す列挙型
 * HTML仕様のトークナイゼーション状態に基づく
 */
enum State
{
    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#data-state
     */
    case Data;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
     */
    case TagOpen;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#end-tag-open-state
     */
    case EndTagOpen;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#tag-name-state
     */
    case TagName;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#before-attribute-name-state
     */
    case BeforeAttributeName;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#attribute-name-state
     */
    case AttributeName;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#after-attribute-name-state
     */
    case AfterAttributeName;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#before-attribute-value-state
     */
    case BeforeAttributeValue;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#attribute-value-(double-quoted)-state
     */
    case AttributeValueDoubleQuoted;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#attribute-value-(single-quoted)-state
     */
    case AttributeValueSingleQuoted;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#attribute-value-(unquoted)-state
     */
    case AttributeValueUnquoted;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#after-attribute-value-(quoted)-state
     */
    case AfterAttributeValueQuoted;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#self-closing-start-tag-state
     */
    case SelfClosingStartTag;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#script-data-state
     */
    case ScriptData;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#script-data-less-than-sign-state
     */
    case ScriptDataLessThanSign;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#script-data-end-tag-open-state
     */
    case ScriptDataEndTagOpen;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#script-data-end-tag-name-state
     */
    case ScriptDataEndTagName;

    /**
     * @see https://html.spec.whatwg.org/multipage/parsing.html#temporary-buffer
     */
    case TemporaryBuffer;
}
