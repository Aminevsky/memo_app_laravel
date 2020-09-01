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

    /**
     * メモを取得する。
     *
     * @param int $id メモID
     * @return array|null 取得成功時は配列、失敗時はnull
     */
    public function fetchById(int $id)
    {
        $model = Memo::find($id);

        if ($model === null) {
            return null;
        }

        return $model->toArray();
    }

    /**
     * メモを更新する。
     *
     * @param int $id メモID
     * @param array $contents 更新後の内容
     * @return array|null 更新成功時は配列、失敗時はnull
     */
    public function update(int $id, array $contents)
    {
        $model = Memo::find($id);

        // TODO $contents が空（count == 0）の場合の例外処理
        // TODO モデルが存在しない場合にどうするか？
        if ($model === null) {
            return null;
        }

        if (array_key_exists('title', $contents)) {
            $model->title = $contents['title'];
        }

        if (array_key_exists('body', $contents)) {
            $model->body = $contents['body'];
        }

        // TODO モデルの更新に失敗した場合にどうするか？
        if (!$model->save()) {
            return null;
        }

        return $model->toArray();
    }

    /**
     * メモを削除する。
     *
     * @param int $id メモID
     * @return bool 削除成功時はtrue、失敗時はfalse
     */
    public function delete(int $id): bool
    {
        return Memo::destroy($id) === 1;
    }

    /**
     * メモを全件取得する。
     *
     * @return array 0件の場合は空配列
     */
    public function fetchAll(): array
    {
        return Memo::all()->toArray();
    }
}
