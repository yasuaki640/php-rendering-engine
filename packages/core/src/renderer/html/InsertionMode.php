<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Html;

/**
 * HTML文書解析時の挿入モードを表すEnum
 * HTMLパーサーがどの段階にあるかを管理する
 *
 * HTML Standard仕様のtree constructionアルゴリズムに対応
 * @see https://html.spec.whatwg.org/multipage/parsing.html#tree-construction
 */
enum InsertionMode: string
{
    /**
     * 初期モード - パース開始時の状態
     */
    case Initial = 'initial';

    /**
     * <html>要素の前 - DOCTYPE処理後
     */
    case BeforeHtml = 'before_html';

    /**
     * <head>要素の前 - <html>要素処理後
     */
    case BeforeHead = 'before_head';

    /**
     * <head>要素内 - メタデータ要素の処理
     */
    case InHead = 'in_head';

    /**
     * <head>要素の後 - <body>要素の前
     */
    case AfterHead = 'after_head';

    /**
     * <body>要素内 - 通常のコンテンツ処理
     */
    case InBody = 'in_body';

    /**
     * テキストモード - script/style要素内
     */
    case Text = 'text';

    /**
     * <body>要素の後 - 終了処理
     */
    case AfterBody = 'after_body';

    /**
     * HTML文書の完全終了後
     */
    case AfterAfterBody = 'after_after_body';
}
