<?php

namespace App\Repositories\Eloquent;

use App\Repositories\MemoRepositoryInterface;
use App\Memo;
use Illuminate\Support\Facades\DB;

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
     * @param int $userId ユーザID
     * @return array|false 作成成功時は配列、失敗時はfalse
     */
    public function create(string $title, string $body, int $userId)
    {
        $model = new Memo();
        $model->title = $title;
        $model->body = $body;
        $model->user_id = $userId;

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
    public function fetchById(int $id): ?array
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
     * @throws \RuntimeException
     */
    public function update(int $id, array $contents): ?array
    {
        $model = Memo::find($id);

        if (count($contents) === 0) {
            throw new \RuntimeException('更新項目を指定してください。');
        }

        if ($model === null) {
            return null;
        }

        if (array_key_exists('title', $contents)) {
            $model->title = $contents['title'];
        }

        if (array_key_exists('body', $contents)) {
            $model->body = $contents['body'];
        }

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
     * メモを全件取得する（キー:ユーザID）。
     *
     * @param int $userId ユーザID
     * @return array 0件の場合は空配列
     */
    public function fetchAllByUserId(int $userId): array
    {
        return Memo::where('user_id', $userId)->get()->toArray();
    }

    /**
     * ユーザIDを取得する。
     *
     * @param int $memoId メモID
     * @return int|null 取得成功時はユーザID、失敗時はnull
     */
    public function fetchUserId(int $memoId): ?int
    {
        $result = DB::table('memos')->select('user_id')
            ->where('id', '=', $memoId)
            ->first();

        if ($result === null) {
            return null;
        }

        return $result->user_id;
    }
}
