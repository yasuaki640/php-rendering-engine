# PHP Rendering Engine

## 開発環境のセットアップ

### PHP CS Fixer

このプロジェクトではコード品質を維持するためにPHP CS Fixerを使用しています。

#### 使用方法

```bash
# コードの自動修正
composer cs-fix

# 修正が必要な箇所をチェック（修正は行わない）
composer cs-check
```

#### 設定

PHP CS Fixerの設定は `.php-cs-fixer.php` ファイルで管理されています。主な設定：

- PSR-12準拠
- 配列の短縮記法
- インポートの自動ソート
- 未使用インポートの削除
- その他コード品質向上のルール

#### 注意事項

PHP 8.4を使用している場合、環境変数 `PHP_CS_FIXER_IGNORE_ENV=1` が設定されています。これはPHP CS FixerがPHP 8.4をまだ正式サポートしていないためです。

## インストール

```bash
composer install
```

## 使用方法

```bash
./bin/hello
```
