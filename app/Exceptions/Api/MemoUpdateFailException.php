<?php

namespace App\Exceptions\Api;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class MemoUpdateFailException メモ更新失敗例外
 * @package App\Exceptions\Api
 */
class MemoUpdateFailException extends CustomException
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
        $response->setTitle( 'メモの更新に失敗しました。');

        return $response->send();
    }
}
