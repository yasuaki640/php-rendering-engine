## プロジェクト概要
このプロジェクトは、PHPで簡易webブラウザを実装するプロジェクトです。 ( ルートディレクトリに存在する `sababook` プロジェクトを参考にしています。)

## 技術スタック
- **言語**: PHP 8.4+
- **依存関係管理**: Composer
- **テストフレームワーク**: PHPUnit
- **コードフォーマッター**: PHP-CS-Fixer

## 開発ガイドライン

### Composer使用方法
- 依存関係の追加: `composer require package/name`
- 依存関係の更新: `composer update`
- オートロード再生成: `composer dump-autoload`

### コーディング規約
- PHP-CS-Fixerを使用してコードフォーマットを統一
- 各パッケージは独立したnamespaceを持つ

### コードフォーマット
- composer.jsonに定義されたコマンドを使用してフォーマットを実行

### テスト
- 各パッケージのルートにtestsディレクトリが存在
- テスト実行: `./vendor/bin/phpunit`
- `-v` と `--verbose` オプションの使用は禁止
- 警告は修正しないが、ユーザーに警告の内容は説明すること
- テストはsababookのテストと一対一で対応するように実装すること
- テストの詳細な出力や警告が表示されない場合は、標準エラー出力に出力されている場合がある。

### ファイル命名規約
- ファイル名はクラス名と同じ
- テストファイルは`ClassNameTest.php`形式

## 開発時の注意点
1. 新しいクラスを作成する際は、既存phpファイルのnamespaceを参考に設定する
2. 依存関係を追加する際は、該当パッケージの`composer.json`を更新する
3. コードを書いた後は必ずPHP-CS-Fixerでフォーマットする（`composer run cs-fix`）
5. 1ファイルには1つのクラスのみを定義する
6. ファイルをrustから移植する場合、それをどのpackageに追加するか、sababookのファイルパスを参考にして配置する

## よく使用するコマンド
```bash
# コードフォーマット実行（推奨）
composer run cs-fix

# コードフォーマットチェック（実際の変更は行わない）
composer run cs-check

# テスト実行（基本）
./vendor/bin/phpunit

# テスト実行（詳細出力）
./vendor/bin/phpunit --verbose --display-warnings --display-deprecations

# 依存関係インストール
composer install

# 依存関係更新
composer update

# オートロード再生成
composer dump-autoload
```
