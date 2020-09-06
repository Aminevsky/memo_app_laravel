<?php

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * バリデーションエラー時のレスポンスを返却する。
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = [];
        foreach ($exception->errors() as $key => $value) {
            $errors[] = [
                'name' => $key,
                'detail' => $value,
            ];
        }

        $response = new ApiErrorResponse();

        return $response->setTitle('入力項目に誤りがあります。')
            ->setAdditions(['errors' => $errors])
            ->setStatus($exception->status)
            ->send();
    }

    /**
     * 例外に対するJSONレスポンスを準備する。
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        $exception = $this->convertExceptionToArray($e);
        $isHttpException = $this->isHttpException($e);

        $title = $exception['title'];
        $additions = isset($exception['info']) ? $exception['info'] : [];
        $status = $isHttpException ? $e->getStatusCode() : 500;
        $headers = $isHttpException ? $e->getHeaders() : [];

        $response = new ApiErrorResponse();

        return $response->setTitle($title)
            ->setAdditions($additions)
            ->setStatus($status)
            ->setHeaders($headers)
            ->setOption(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ->send();
    }

    /**
     * 例外を配列へ変換する。
     *
     * @param Throwable $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        if (config('app.debug')) {
            return [
                'title' => $e->getMessage(),
                'info' => [
                    'exception' => get_class(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->map(function ($trace) {
                        return Arr::except($trace, ['args']);
                    })->all(),
                ]
            ];
        }

        return [
            'title' => $this->isHttpException($e) ? $e->getMessage() : 'システムでエラーが発生しました。',
        ];
    }
}
