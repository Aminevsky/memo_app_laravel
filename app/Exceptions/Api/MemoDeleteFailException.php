<?php

namespace App\Exceptions\Api;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class MemoDeleteFailException メモ削除失敗例外
 * @package App\Exceptions\Api
 */
class MemoDeleteFailException extends CustomException
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
        $response->setTitle( 'メモの削除に失敗しました。');

        return $response->send();
    }
}
