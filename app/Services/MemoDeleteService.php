<?php

namespace App\Services;

use App\Repositories\MemoRepositoryInterface;

class MemoDeleteService
{
    /** @var MemoRepositoryInterface  */
    private MemoRepositoryInterface $memoRepository;

    /**
     * コンストラクタ
     *
     * @param MemoRepositoryInterface $memoRepository
     */
    public function __construct(MemoRepositoryInterface $memoRepository)
    {
        $this->memoRepository = $memoRepository;
    }

    /**
     * メモ情報を削除する。
     *
     * @param int $id メモID
     * @return bool 削除成功時はtrue、失敗時はfalse
     */
    public function delete(int $id): bool
    {
        return $this->memoRepository->delete($id);
    }
}
