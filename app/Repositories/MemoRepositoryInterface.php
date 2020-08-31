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
}
