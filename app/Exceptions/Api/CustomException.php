<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CustomException
 * @package App\Exceptions\Api
 */
abstract class CustomException extends Exception
{
    /**
     * 例外をJSONレスポンスとして返却する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    abstract public function render(Request $request): JsonResponse;
}
