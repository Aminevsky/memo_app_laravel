<?php

namespace App\Exceptions\Api;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class MemoNotFoundException メモ不存在例外
 * @package App\Exceptions\Api
 */
class MemoNotFoundException extends CustomException
{
    /**
     * 例外をJSONレスポンスとして返却する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $response = new ApiErrorResponse();

        return $response->setTitle('メモが存在しません。')
            ->setStatus(Response::HTTP_NOT_FOUND)
            ->send();
    }
}
