<?php

namespace App\Http\Presenters\Api;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiPresenter
 * @package App\Http\Presenters\Api
 */
class ApiPresenter
{
    /**
     * エラーレスポンスを返却する。
     *
     * @param string $title タイトル
     * @param array $addition 拡張情報
     * @param int $status HTTPステータスコード
     * @return \Illuminate\Http\JsonResponse
     * @see https://tools.ietf.org/html/rfc7807 RFC7807 (Problem Details for HTTP APIs)
     */
    public function responseError(
        string $title,
        array $addition = [],
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse
    {
        $contents = [];
        $contents['title'] = $title;

        if (count($addition) > 0) {
            $contents = array_merge($contents, $addition);
        }

        return response()
            ->json($contents, $status)
            ->withHeaders([
                'Content-Type' => 'application/problem+json'
            ]);
    }
}
