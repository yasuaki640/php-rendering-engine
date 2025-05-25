# GitHub Copilot Instructions

## プロジェクト概要
このプロジェクトは、PHPで簡易webブラウザを実装するプロジェクトです。 ( ルートディレクトリに存在する `sababook` プロジェクトを参考にしています。)
最終的には、HTMLとCSSを解析してレンダリングし、JavaScriptを実行できるブラウザを目指しています。

## プロジェクトの目的

PHPで自作しブラウザの挙動を理解する

Webブラウザは、開発者にとってもユーザーにとっても、もはや日常の一部となっているほど身近なソフトウエアですが、近年のブラウザはあまりにも高機能かつ巨大になってしまったため、その仕組みを詳しく理解することは困難です。そこで、シンプルなブラウザをPHPを用いて実装することによって、ブラウザ上でWebサイトを開くまでに何が起きているのかを理解することを目的とします。

## 技術スタック
- **言語**: PHP 8.4+
- **依存関係管理**: Composer
- **テストフレームワーク**: PHPUnit
- **コードフォーマッター**: PHP-CS-Fixer

## [WIP] プロジェクト構成
```
php-rendering-engine/
├── packages/
│   ├── core/          # HTTPレスポンス、ヘッダー処理
│   ├── net/           # HTTPクライアント
│   └── uri-parser/    # URI解析
├── src/               # メインアプリケーション
└── bin/               # 実行可能ファイル
```

## 開発ガイドライン

### Composer使用方法
- **重要**: このプロジェクトでは、ルートディレクトリに存在する`composer.phar`を使用してcomposerコマンドを実行してください
- 依存関係の追加: `php composer.phar require package/name`
- 依存関係の更新: `php composer.phar update`
- オートロード再生成: `php composer.phar dump-autoload`

### コーディング規約
- PHP-CS-Fixerを使用してコードフォーマットを統一
- PSR-4オートローディング規約に従う
- 各パッケージは独立したnamespaceを持つ

### テスト
- PHPUnitを使用
- 各パッケージにテストディレクトリが存在
- テスト実行: `./vendor/bin/phpunit`

### ファイル命名規約
- クラス名はPascalCase
- ファイル名はクラス名と同じ
- テストファイルは`ClassNameTest.php`形式

## 開発時の注意点
1. 新しいクラスを作成する際は、適切なnamespaceを設定する
2. 依存関係を追加する際は、該当パッケージの`composer.json`を更新する
3. コードを書いた後は必ずPHP-CS-Fixerでフォーマットする
4. 新機能には対応するテストを作成する

## よく使用するコマンド
```bash
# コードフォーマット実行
./vendor/bin/php-cs-fixer fix

# テスト実行
./vendor/bin/phpunit

# 依存関係インストール
php composer.phar install
```
