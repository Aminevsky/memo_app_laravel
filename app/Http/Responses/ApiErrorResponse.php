<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class ApiErrorResponse
 * @package App\Http\Responses\Api
 */
class ApiErrorResponse extends ApiResponse
{
    /** @var string タイトル */
    private string $title;

    /** @var array 追加情報 */
    private array $additions;

    /** @var int HTTPステータスコード */
    private int $status;

    /** @var array ヘッダ */
    private array $headers;

    /** @var int オプション */
    private int $option;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->title = '';
        $this->additions = [];
        $this->status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->headers = [];
        $this->option = 0;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param array $additions
     * @return self
     */
    public function setAdditions(array $additions): self
    {
        $this->additions = $additions;

        return $this;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param int $option
     * @return self
     */
    public function setOption(int $option): self
    {
        $this->option = $option;

        return $this;
    }

    /**
     * レスポンスを返却する。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(): JsonResponse
    {
        $contents = [];
        $contents['title'] = $this->title;

        if (count($this->additions) > 0) {
            $contents = array_merge($contents, $this->additions);
        }

        return response()
            ->json($contents, $this->status, $this->headers, $this->option)
            ->withHeaders([
                'Content-Type' => 'application/problem+json'
            ]);
    }
}
