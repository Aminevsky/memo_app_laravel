<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponse
 * @package App\Http\Responses\Api
 */
abstract class ApiResponse
{
    /**
     * レスポンスを返却する。
     *
     * @return JsonResponse
     */
    abstract public function send(): JsonResponse;
}
