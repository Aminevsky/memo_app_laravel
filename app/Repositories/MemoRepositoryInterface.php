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
     * @return array|false 作成成功時は配列、失敗時はfalse
     */
    function create(string $title, string $body);

    /**
     * メモを取得する。
     *
     * @param int $id メモID
     * @return array|null 取得成功時は配列、失敗時はnull
     */
    function fetchById(int $id);
}
