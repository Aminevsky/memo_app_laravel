<?php

namespace App\Services\User;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\RedisManager;
use Tymon\JWTAuth\Contracts\Providers\Storage as StorageContract;

class TokenStorage implements StorageContract
{
    /** @var string 接続先DB名 */
    const DB_NAME = 'token';

    /** @var Connection Redis接続オブジェクト */
    private Connection $connection;

    /**
     * コンストラクタ
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->connection = $redisManager->connection(self::DB_NAME);
    }

    /**
     * トークンをRedisに保管する。
     *
     * @param string $key キー
     * @param mixed $value 設定値
     * @param int $minutes 有効時間（分単位）
     */
    public function add($key, $value, $minutes)
    {
        $seconds = $minutes * 60;
        $this->connection->setEx($key, $seconds, $value);
    }

    /**
     * トークンをRedisに永久的に保管する。
     *
     * @param string $key キー
     * @param mixed $value 設定値
     */
    public function forever($key, $value)
    {
        $this->connection->set($key, $value);
    }

    /**
     * トークンを取得する。
     *
     * @param string $key キー
     * @return mixed 取得値
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     * トークンを削除する。
     *
     * @param string $key キー
     * @return bool 成功時:true、失敗時:false
     */
    public function destroy($key)
    {
        return $this->connection->del($key) === 1;
    }

    /**
     * DB内のデータを削除する。
     */
    public function flush()
    {
        $this->connection->flushDb();
    }
}
