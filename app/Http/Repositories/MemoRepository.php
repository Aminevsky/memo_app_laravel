<?php

namespace App\Http\Repositories;

use App\Memo;

/**
 * Class MemoRepository メモリポジトリ
 * @package App\Http\Repositories
 */
class MemoRepository
{
    /**
     * メモを新規作成する。
     *
     * @param string $title タイトル
     * @param string $body 本文
     * @return bool
     */
    public function create(string $title, string $body)
    {
        $model = new Memo();
        $model->title = $title;
        $model->body = $body;

        return $model->save();
    }
}
