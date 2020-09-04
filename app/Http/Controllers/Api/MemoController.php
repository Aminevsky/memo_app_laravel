<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MemoCreateService;
use App\Services\MemoDeleteService;
use App\Services\MemoListService;
use App\Services\MemoShowService;
use App\Services\MemoUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class MemoController
 * @package App\Http\Controllers\Api
 */
class MemoController extends Controller
{
    /**
     * メモ一覧API
     *
     * @param \App\Services\MemoListService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(MemoListService $service): JsonResponse
    {
        $result = $service->fetchAll();

        return response()->json($result);
    }

    /**
     * メモ新規作成API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\MemoCreateService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, MemoCreateService $service): JsonResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'body'  => 'required|max:5000',
        ]);

        $title = $request->title;
        $body = $request->body;

        $result = $service->create($title, $body);

        if (!$result) {
            return response()->json([
                'error' => '作成に失敗しました。',
            ]);
        }

        return response()->json($result);
    }

    /**
     * メモ詳細API
     *
     * @param  int  $id
     * @param \App\Services\MemoShowService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, MemoShowService $service): JsonResponse
    {
        $memo = $service->show($id);

        // TODO メモが存在しない場合のエラー

        return response()->json($memo);
    }

    /**
     * メモ更新API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param \App\Services\MemoUpdateService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id, MemoUpdateService $service): JsonResponse
    {
        if (!$this->existsUpdateInputItem($request)) {
            return response()->json([
                'message' => 'タイトルまたは本文のいずれかを指定してください。'
            ], 400);
        }

        $request->validate([
            'title' => 'max:255',
            'body'  => 'max:5000'
        ]);

        $contents = [];

        if ($request->title !== null) {
            $contents['title'] = $request->title;
        }

        if ($request->body !== null) {
            $contents['body'] = $request->body;
        }

        $memo = $service->update($id, $contents);

        // TODO 更新に失敗した場合のエラー

        return response()->json($memo);
    }

    /**
     * 更新項目が入力に存在するかをチェックする。
     *
     * @param \Illuminate\Http\Request $request
     * @return bool 存在時はtrue、不存在時はfalse
     */
    private function existsUpdateInputItem(Request $request): bool
    {
        $fields = ['title', 'body'];

        foreach ($fields as $field) {
            if (isset($request->$field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * メモ削除API
     *
     * @param  int  $id
     * @param \App\Services\MemoDeleteService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, MemoDeleteService $service): JsonResponse
    {
        $result = $service->delete($id);

        // TODO 削除に失敗した場合のエラー

        return response()->json([
            'result' => $result,
        ]);
    }
}
