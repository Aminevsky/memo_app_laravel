<?php

namespace App\Repositories\Eloquent;

use App\Repositories\MemoRepositoryInterface;
use App\Memo;

/**
 * Class MemoRepository メモリポジトリ
 * @package App\Repositories
 */
class MemoRepository implements MemoRepositoryInterface
{
    /**
     * メモを新規作成する。
     *
     * @param string $title タイトル
     * @param string $body 本文
     * @return bool true:作成成功、false:作成失敗
     */
    public function create(string $title, string $body): bool
    {
        $model = new Memo();
        $model->title = $title;
        $model->body = $body;

        return $model->save();
    }
}
