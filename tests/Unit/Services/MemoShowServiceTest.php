<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
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
        ];

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('fetchById')
            ->once()
            ->andReturn($expected);
        app()->instance(MemoRepositoryInterface::class, $mockRepo);

        $service = app()->make(MemoShowService::class);
        $result = $service->show($id);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }
}
