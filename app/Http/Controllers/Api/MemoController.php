<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\MemoDeleteFailException;
use App\Exceptions\Api\MemoNotFoundException;
use App\Exceptions\Api\MemoStoreFailException;
use App\Exceptions\Api\MemoUpdateFailException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\MemoCreateService;
use App\Services\MemoDeleteService;
use App\Services\MemoListService;
use App\Services\MemoShowService;
use App\Services\MemoUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class MemoController
 * @package App\Http\Controllers\Api
 */
class MemoController extends Controller
{
    /** @var int タイトル最大文字数 */
    const TITLE_MAX_LEN = 255;

    /** @var int 本文最大文字数 */
    const BODY_MAX_LEN = 5000;

    /** @var ApiSuccessResponse 成功時のレスポンス */
    private ApiSuccessResponse $successResponse;

    /**
     * コンストラクタ
     *
     * @param ApiSuccessResponse $successResponse
     */
    public function __construct(ApiSuccessResponse $successResponse)
    {
        $this->successResponse = $successResponse;
    }

    /**
     * メモ一覧API
     *
     * @param \App\Services\MemoListService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(MemoListService $service): JsonResponse
    {
        $result = $service->fetchAll();

        return $this->successResponse->setContent($result)->send();
    }

    /**
     * メモ新規作成API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\MemoCreateService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\MemoStoreFailException
     */
    public function store(Request $request, MemoCreateService $service): JsonResponse
    {
        Validator::make($request->all(), [
            'title' => ['required', $this->getRuleTitleMax()],
            'body'  => ['required', $this->getRuleBodyMax()],
        ])->validate();

        $result = $service->create($request->title, $request->body);

        if (!$result) {
            throw new MemoStoreFailException();
        }

        return $this->successResponse->setContent($result)->send();
    }

    /**
     * メモ詳細API
     *
     * @param  int  $id
     * @param \App\Services\MemoShowService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\MemoNotFoundException
     */
    public function show(int $id, MemoShowService $service): JsonResponse
    {
        $memo = $service->show($id);

        if ($memo === null) {
            throw new MemoNotFoundException();
        }

        return $this->successResponse->setContent($memo)->send();
    }

    /**
     * メモ更新API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param \App\Services\MemoUpdateService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\MemoUpdateFailException
     */
    public function update(Request $request, int $id, MemoUpdateService $service): JsonResponse
    {
        $errorMsgs = [
            'required_without_all' => 'タイトルまたは本文のいずれかを指定してください。',
        ];

        Validator::make($request->all(), [
            'title' => [$this->getRuleTitleMax(), 'required_without_all:body'],
            'body'  => [$this->getRuleBodyMax(), 'required_without_all:title'],
        ], $errorMsgs)->validate();

        $contents = [];

        if ($request->title !== null) {
            $contents['title'] = $request->title;
        }
        if ($request->body !== null) {
            $contents['body'] = $request->body;
        }

        $memo = $service->update($id, $contents);

        if ($memo === null) {
            throw new MemoUpdateFailException();
        }

        return $this->successResponse->setContent($memo)->send();
    }

    /**
     * メモ削除API
     *
     * @param  int  $id
     * @param \App\Services\MemoDeleteService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\MemoDeleteFailException
     */
    public function destroy(int $id, MemoDeleteService $service): JsonResponse
    {
        $result = $service->delete($id);

        if (!$result) {
            throw new MemoDeleteFailException();
        }

        return $this->successResponse->setContent(['result' => $result])->send();
    }

    /**
     * タイトルの最大文字数のバリデーションルールを返却する。
     *
     * @return string
     */
    private function getRuleTitleMax(): string
    {
        return 'max:' . constant('self::TITLE_MAX_LEN');
    }

    /**
     * 本文の最大文字数のバリデーションルールを返却する。
     *
     * @return string
     */
    private function getRuleBodyMax(): string
    {
        return 'max:' . constant('self::BODY_MAX_LEN');
    }
}
