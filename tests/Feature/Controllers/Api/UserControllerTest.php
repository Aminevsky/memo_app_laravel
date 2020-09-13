<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, ResponseAssertTrait;

    /***************************************************************
     * login()
     ***************************************************************/
    /**
     * @test
     */
    public function ログイン成功時に正常なレスポンスが返却されること()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->postJson(route('user.login'), [
            'email'     => $user->email,
            'password'  => 'password',
        ]);

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure(['access_token']);
    }

    /**
     * @test
     */
    public function ログイン失敗時にエラーレスポンスが返却されること()
    {
        $email = 'test_invalid@example.com';
        $password = 'password_invalid';

        $response = $this->postJson(route('user.login'), [
            'email'     => $email,
            'password'  => $password,
        ]);

        $msg = 'Unauthenticated.';
        $this->assertClientErrorResponse($response, Response::HTTP_UNAUTHORIZED, $msg);
    }

    /**
     * @test
     * @dataProvider dataProviderForLoginValidation
     */
    public function ログイン時にバリデーションが行われること($email, $password, $errors)
    {
        $response = $this->postJson(route('user.login'), [
            'email'     => $email,
            'password'  => $password,
        ]);

        $this->assertValidationErrorResponse($response, $errors);
    }

    /**
     * データプロバイダ（バリデーションエラー）
     *
     * @return array[]
     */
    public function dataProviderForLoginValidation()
    {
        return [
            'メールアドレス：空欄' => [
                '',
                'password',
                [
                    [
                        'name' => 'email',
                        'detail' => ['メールアドレスを指定してください。'],
                    ],
                ],
            ],
            'メールアドレス：形式不正' => [
                'test_email',
                'password',
                [
                    [
                        'name' => 'email',
                        'detail' => ['メールアドレスの形式が正しくありません。'],
                    ],
                ],
            ],
            'パスワード：空欄' => [
                'test@example.com',
                '',
                [
                    [
                        'name' => 'password',
                        'detail' => ['パスワードを指定してください。'],
                    ],
                ],
            ],
        ];
    }

    /***************************************************************
     * logout()
     ***************************************************************/
    /**
     * @test
     */
    public function ログアウト時に正常なレスポンスが返却されること()
    {
        $user = factory(\App\User::class)->create();
        $token = $this->generateToken($user);

        $response = $this->withToken($token)->postJson(route('user.logout'), []);

        $this->assertSuccessResponse($response);
        $response->assertExactJson(['result' => true]);
    }

    /***************************************************************
     * refresh()
     ***************************************************************/
    /**
     * @test
     */
    public function リフレッシュ時に正常なレスポンスが返却されること()
    {
        $user = factory(\App\User::class)->create();
        $token = $this->generateToken($user);

        $response = $this->withToken($token)->postJson(route('user.refresh'), []);

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure(['access_token']);
    }

    /***************************************************************
     * 共通
     ***************************************************************/
    /**
     * トークンを生成する。
     *
     * @param \Tymon\JWTAuth\Contracts\JWTSubject $user
     * @return mixed
     */
    private function generateToken(JWTSubject $user)
    {
        return auth()->fromSubject($user);
    }
}
