<?php

namespace App\Exceptions\Api;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class MemoNotAuthorizedException メモ認可例外
 * @package App\Exceptions\Api
 */
class MemoNotAuthorizedException extends CustomException
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
        $response->setTitle('メモへのアクセスが許可されていません。')
            ->setStatus(Response::HTTP_FORBIDDEN);

        return $response->send();
    }
}
