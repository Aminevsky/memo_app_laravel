<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class ApiSuccessResponse
 * @package App\Http\Responses
 */
class ApiSuccessResponse extends ApiResponse
{
    /** @var mixed 返却内容 */
    private $content;

    /**
     * @param mixed $content
     * @return self
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * レスポンスを返却する。
     *
     * @return JsonResponse
     */
    public function send(): JsonResponse
    {
        return response()->json($this->content);
    }
}
