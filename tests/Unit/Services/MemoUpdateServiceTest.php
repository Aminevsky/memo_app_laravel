<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoUpdateService;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class MemoUpdateServiceTest extends TestCase
{
    /**
     * @test
     */
    public function リポジトリの更新メソッドを呼ぶこと()
    {
        $id = 1;
        $afterTitle = 'タイトル_更新後';
        $afterBody = '本文_更新後';

        $expected = [
            'id'    => $id,
            'title' => $afterTitle,
            'body'  => $afterBody,
            'created_at' => Carbon::now()->toJSON(),
            'updated_at' => Carbon::now()->toJSON(),
        ];

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('update')
            ->once()
            ->andReturn($expected);

        $service = new MemoUpdateService($mockRepo);

        $contents = [
            'title' => $afterTitle,
            'body'  => $afterBody,
        ];

        $result = $service->update($id, $contents);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }
}
