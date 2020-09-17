<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoShowService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Mockery;

class MemoShowServiceTest extends TestCase
{
    /**
     * @test
     */
    public function リポジトリでメモ情報を取得できること()
    {
        $id = 1;

        $expected = [
            'id'    => $id,
            'title' => 'タイトルテスト',
            'body'  => '本文テスト',
            'created_at' => Carbon::now()->toJSON(),
            'updated_at' => Carbon::now()->toJSON(),
            'user_id' => 1,
        ];

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('fetchById')
            ->once()
            ->andReturn($expected);

        $service = new MemoShowService($mockRepo);
        $result = $service->show($id);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }
}
