# Memo App ([English](./README.md) / Japanese)

この Laravel プロジェクトでは、メモアプリの開発を行っています。

## 要件

- PHP 7.4
- Laravel 7
- MySQL 5.7
- Redis 6

## 機能

- API
    - メモの新規作成、取得、更新、削除を行うことができます
        - **注意:** これらの機能を使うには JWT 認証が必要です。
 
## 開発方針

- [jwt-auth](https://github.com/tymondesigns/jwt-auth) による JWT 認証
- リポジトリパターン
- [RFC 7807](https://tools.ietf.org/html/rfc7807) ベースのエラーレスポンス
- [PHPUnit](https://github.com/sebastianbergmann/phpunit) によるテストコード
- [PHPStan](https://github.com/phpstan/phpstan) によるコード解析
