<?php

namespace App\Services;

use App\Repositories\MemoRepositoryInterface;

/**
 * Class MemoUpdateService
 * @package App\Services
 */
class MemoUpdateService
{
    use MemoHelperTrait;

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
     * メモ情報を更新する。
     *
     * @param int $id メモID
     * @param array $contents 更新後の内容
     * @return array|null 更新成功時は配列、失敗時はnull
     */
    public function update(int $id, array $contents): ?array
    {
        return $this->memoRepository->update($id, $contents);
    }
}
