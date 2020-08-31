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
     * @return array|false 作成成功時は配列、失敗時はfalse
     */
    public function create(string $title, string $body)
    {
        $model = new Memo();
        $model->title = $title;
        $model->body = $body;

        if (!$model->save()) {
            return false;
        }

        return $model->toArray();
    }
}