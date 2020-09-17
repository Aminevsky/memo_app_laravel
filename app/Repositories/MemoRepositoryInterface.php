<?php

namespace App\Repositories;

/**
 * Interface MemoRepositoryInterface
 * @package App\Repositories
 */
interface MemoRepositoryInterface
{
    /**
     * メモを新規作成する。
     *
     * @param string $title タイトル
     * @param string $body 本文
     * @param int $userId ユーザID
     * @return array|false 作成成功時は配列、失敗時はfalse
     */
    function create(string $title, string $body, int $userId);

    /**
     * メモを取得する。
     *
     * @param int $id メモID
     * @return array|null 取得成功時は配列、失敗時はnull
     */
    function fetchById(int $id): ?array;

    /**
     * メモを更新する。
     *
     * @param int $id メモID
     * @param array $contents 更新後の内容
     * @return array|null 更新成功時は配列、失敗時はnull
     */
    function update(int $id, array $contents): ?array;

    /**
     * メモを削除する。
     *
     * @param int $id メモID
     * @return bool 削除成功時はtrue、失敗時はfalse
     */
    function delete(int $id): bool;

    /**
     * メモを全件取得する（キー:ユーザID）。
     *
     * @param int $userId ユーザID
     * @return array 0件の場合は空配列
     */
    function fetchAllByUserId(int $userId): array;

    /**
     * ユーザIDを取得する。
     *
     * @param int $memoId メモID
     * @return int|null 取得成功時はユーザID、失敗時はnull
     */
    function fetchUserId(int $memoId): ?int;
}
