<?php

namespace Tests\Unit\Services;

use App\Repositories\MemoRepositoryInterface;
use App\Services\MemoHelperTrait;
use PHPUnit\Framework\TestCase;
use Mockery;

class TargetClass
{
    use MemoHelperTrait;

    private MemoRepositoryInterface $memoRepository;

    public function __construct(MemoRepositoryInterface $memoRepository)
    {
        $this->memoRepository = $memoRepository;
    }
}

class MemoHelperTraitTest extends TestCase
{
   /**
    * @test
    */
   public function ユーザIDが等しい場合はtrueを返却すること()
   {
       $memoId = 1;
       $userId = 10;

       $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
       $mockRepo->shouldReceive('fetchUserId')
           ->once()
           ->andReturn($userId);

       $target = new TargetClass($mockRepo);
       $result = $target->isAuthorized($memoId, $userId);

       $this->assertTrue($result);
   }

   /**
    * @test
    */
   public function ユーザIDが異なる場合はfalseを返却すること()
   {
       $memoId = 1;
       $userIds = [10, 11];

       $mockRepo = Mockery::mock(MemoRepositoryInterface::class);
       $mockRepo->shouldReceive('fetchUserId')
           ->once()
           ->andReturn($userIds[0]);

       $target = new TargetClass($mockRepo);
       $result = $target->isAuthorized($memoId, $userIds[1]);

       $this->assertFalse($result);
   }
}
