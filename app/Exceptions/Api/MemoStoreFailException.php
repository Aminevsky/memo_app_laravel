<?php

namespace App\Exceptions\Api;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class MemoStoreFailException メモ新規作成例外
 * @package App\Exceptions\Api
 */
class MemoStoreFailException extends CustomException
{
    /**
     * 例外をJSONレスポンスとして返却する。
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $response = new ApiErrorResponse();
        $response->setTitle( 'メモの新規作成に失敗しました。');

        return $response->send();
    }
}
