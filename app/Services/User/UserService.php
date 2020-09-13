<?php

namespace App\Services\User;

use Illuminate\Support\Facades\Auth;

class UserService
{
    /** @var Auth 認証ガード */
    private Auth $auth;

    /**
     * コンストラクタ
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * ログインする。
     *
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return string|false 成功時:トークン、失敗時:false
     * @see Tymon\JWTAuth\JWTGuard
     */
    public function login(string $email, string $password)
    {
        return $this->auth::attempt([
            'email'     => $email,
            'password'  => $password,
        ]);
    }

    /**
     * ログアウトする。
     *
     * @see Tymon\JWTAuth\JWTGuard
     */
    public function logout(): void
    {
        $this->auth::logout();
    }

    /**
     * トークンをリフレッシュする。
     *
     * @return string リフレッシュ後のトークン
     */
    public function refresh(): string
    {
        return $this->auth::refresh();
    }
}
