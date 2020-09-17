<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoListService;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class MemoListServiceTest extends TestCase
{
    /**
     * @test
     */
    public function 全件取得するメソッドを呼ぶこと()
    {
        $userId = 1;

        $generator = function() use ($userId) {
            $records = [];

            for ($i = 1; $i <= 2; $i++) {
                $records[] = [
                    'id' => $i,
                    'title' => 'タイトルテスト',
                    'body' => '本文テスト',
                    'created_at' => Carbon::now()->toJSON(),
                    'updated_at' => Carbon::now()->toJSON(),
                    'user_id' => $userId,
                ];
            }

            return $records;
        };
        $expected = $generator();

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('fetchAllByUserId')
            ->once()
            ->withAnyArgs()
            ->andReturn($expected);

        $service = new MemoListService($mockRepo);
        $result = $service->fetchAllByUserId($userId);

        $this->assertIsArray($result);
    }
}
