<?php

namespace App\Services;

use App\Repositories\MemoRepositoryInterface;

/**
 * Class MemoListService
 * @package App\Services
 */
class MemoListService
{
    /** @var MemoRepositoryInterface */
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
     * メモを全件取得する。
     *
     * @return array 0件の場合は空配列
     */
    public function fetchAll(): array
    {
        return $this->memoRepository->fetchAll();
    }
}
