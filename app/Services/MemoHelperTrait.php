<?php

namespace App\Services;

/**
 * Trait MemoHelperTrait
 * @package App\Services
 */
trait MemoHelperTrait
{
    /**
     * メモへのアクセスが認可されているかを判定する。
     *
     * @param int $memoId メモID
     * @param int $userId ユーザID
     * @return bool true:認可されている、false:認可されていない
     */
    public function isAuthorized(int $memoId, int $userId): bool
    {
        return $userId === $this->memoRepository->fetchUserId($memoId);
    }
}
