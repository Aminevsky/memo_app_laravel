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
     * @return bool true:作成成功、false:作成失敗
     */
    function create(string $title, string $body): bool;
}
