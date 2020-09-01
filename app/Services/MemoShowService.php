<?php

namespace App\Services;

use App\Repositories\MemoRepositoryInterface;

class MemoShowService
{
    /**
     * @var MemoRepositoryInterface
     */
    private $memoRepository;

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
     * メモ情報を取得する。
     *
     * @param int $id メモID
     * @return array|null 取得成功時は配列、失敗時はnull
     */
    public function show(int $id)
    {
        return $this->memoRepository->fetchById($id);
    }
}
