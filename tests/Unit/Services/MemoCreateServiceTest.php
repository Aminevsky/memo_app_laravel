<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoCreateService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Carbon\Carbon;

class MemoCreateServiceTest extends TestCase
{
    /**
     * @test
     */
    public function リポジトリで新規作成後のデータを取得できること()
    {
        $title = 'タイトルテスト';
        $body = '本文テスト';
        $userId = 1;

        $expected = [
            'id'    => 1,
            'title' => $title,
            'body'  => $body,
            'created_at' => Carbon::now()->toJSON(),
            'updated_at' => Carbon::now()->toJSON(),
            'user_id' => $userId,
        ];

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('create')
            ->once()
            ->andReturn($expected);

        $service = new MemoCreateService($mockRepo);
        $result = $service->create($title, $body, $userId);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }
}
