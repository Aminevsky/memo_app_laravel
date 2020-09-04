<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

/**
 * Trait ResponseAssertTrait
 * @package Tests\Feature\Controllers\Api
 */
trait ResponseAssertTrait
{
    /**
     * 正常時のレスポンスであるかを検証する。
     *
     * @param TestResponse $response
     */
    public function assertSuccessResponse(TestResponse $response)
    {
        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * サーバエラーが発生した場合のレスポンスであるかを検証する。
     *
     * @param TestResponse $response
     * @param string $title
     */
    public function assertServerErrorResponse(TestResponse $response, string $title)
    {
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertHeader('Content-Type', 'application/problem+json');

        $errors = [
            'title' => $title,
        ];
        $response->assertExactJson($errors);
    }

    /**
     * バリデーションエラーが発生した場合のレスポンスであるかを検証する。
     *
     * @param TestResponse $response
     * @param array $errors
     */
    public function assertValidationErrorResponse(TestResponse $response, array $errors)
    {
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertHeader('Content-Type', 'application/problem+json');

        $errors = [
            'title' => '入力項目に誤りがあります。',
            'errors' => $errors,
        ];

        $response->assertExactJson($errors);
    }

    /**
     * クライアントエラー（バリデーションエラー以外）が発生した場合のレスポンスであるかを検証する。
     *
     * @param TestResponse $response
     * @param int $status
     * @param string $title
     */
    public function assertClientErrorResponse(TestResponse $response, int $status, string $title)
    {
        $response->assertStatus($status);
        $response->assertHeader('Content-Type', 'application/problem+json');

        $errors = [
            'title' => $title,
        ];

        $response->assertExactJson($errors);
    }
}
