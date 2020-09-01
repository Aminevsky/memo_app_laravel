<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoDeleteService;
use Mockery;
use PHPUnit\Framework\TestCase;

class MemoDeleteServiceTest extends TestCase
{
    /**
     * @test
     */
    public function リポジトリの削除メソッドを呼ぶこと()
    {
        $id = 1;

        $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
        $mockRepo->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        $service = new MemoDeleteService($mockRepo);
        $result = $service->delete($id);

        $this->assertTrue($result);
    }
}
