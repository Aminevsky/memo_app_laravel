<?php

namespace Tests\Feature\Services;

use App\Services\User\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Redis\RedisManager;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /***************************************************************
     * login()
     ***************************************************************/
    /**
     * @test
     */
    public function トークン生成に成功した場合はトークンを返却すること()
    {
        $id = 1;
        $email = 'testuser@example.com';
        $password = 'password';

        factory(\App\User::class)->create([
            'id' => $id,
            'email' => $email,
        ]);

        $service = app()->make(UserService::class);
        $token = $service->login($email, $password);

        $tokenParts = explode('.', $token);
        $payload = $tokenParts[1];
        $base64Decoded = base64_decode($payload);
        $jsonDecoded = json_decode($base64Decoded, true);

        $this->assertIsString($token);

        // トークンの識別子がIDであること
        $this->assertSame($id, $jsonDecoded['sub']);
    }

    /**
     * @test
     */
    public function トークン生成に失敗した場合はfalseを返却すること()
    {
        $email = 'test_invalid@example.com';
        $password = 'password_invalid';

        $service = app()->make(UserService::class);
        $token = $service->login($email, $password);

        $this->assertFalse($token);
    }

    /***************************************************************
     * logout()
     ***************************************************************/
    /**
     * @test
     */
    public function トークン削除に成功した場合はブラックリストに追加されること()
    {
        $user = factory(\App\User::class)->create();
        auth()->login($user);

        $connection = $this->getRedisConnection();
        $beforeCount = $connection->dbSize();

        $service = app()->make(UserService::class);
        $service->logout();

        $afterCount = $connection->dbSize();

        $this->assertSame(1, $afterCount - $beforeCount);
    }

    /***************************************************************
     * refresh()
     ***************************************************************/
    /**
     * @test
     */
    public function リフレッシュしたトークンが返却されること()
    {
        $user = factory(\App\User::class)->create();
        $token = auth()->login($user);

        $service = app()->make(UserService::class);
        $refreshedToken = $service->refresh();

        $this->assertIsString($refreshedToken);
        $this->assertNotSame($refreshedToken, $token);
    }

    /**
     * Redis（トークンDB）接続を取得する。
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getRedisConnection()
    {
        return app()->make(RedisManager::class)->connection('token');
    }
}
