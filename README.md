# Memo App (English / [Japanese](./README.ja.md))

In this Laravel project, I'm developing Memo App.

## Requirements

- PHP 7.4
- Laravel 7
- MySQL 5.7
- Redis 6

## Features

- API
    - Memo's Create, Read, Update, Delete
        - **Note:** These features require JWT authentication.
    - User's Login, Logout, Token Refresh

## Develop Policy

- JWT Authentication by [jwt-auth](https://github.com/tymondesigns/jwt-auth)
- Repository Pattern
- Error Response based by [RFC 7807](https://tools.ietf.org/html/rfc7807)
- Test Code by [PHPUnit](https://github.com/sebastianbergmann/phpunit)
- Code Analytics by [PHPStan](https://github.com/phpstan/phpstan)
