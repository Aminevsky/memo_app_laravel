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
        $generator = function() {
            $records = [];

            for ($i = 1; $i <= 2; $i++) {
                $records[] = [
                    'id' => $i,
                    'title' => 'タイトルテスト',
                    'body' => '本文テスト',
                    'created_at' => Carbon::now()->toJSON(),
                    'updated_at' => Carbon::now()->toJSON(),
                ];
            }

            return $records;
        };
        $expected = $generator();

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('fetchAll')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);

        $service = new MemoListService($mockRepo);
        $result = $service->fetchAll();

        $this->assertIsArray($result);

    }
}
