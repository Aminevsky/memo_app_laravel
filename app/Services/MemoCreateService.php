<?php

namespace App\Services;

use App\Repositories\MemoRepositoryInterface;

/**
 * Class MemoCreateService メモ新規作成サービス
 * @package App\Services
 */
class MemoCreateService
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
     * メモを新規作成する。
     *
     * @param string $title タイトル
     * @param string $body 本文
     * @param int $userId ユーザID
     * @return array|false 作成成功時は配列、失敗時はfalse
     */
    public function create(string $title, string $body, int $userId)
    {
        return $this->memoRepository->create($title, $body, $userId);
    }
}
