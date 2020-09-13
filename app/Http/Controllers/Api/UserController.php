<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /** @var ApiSuccessResponse  */
    private ApiSuccessResponse $successResponse;

    /** @var UserService  */
    private UserService $userService;

    /**
     * コンストラクタ
     *
     * @param ApiSuccessResponse $successResponse
     * @param UserService $userService
     */
    public function __construct(ApiSuccessResponse $successResponse, UserService $userService)
    {
        $this->middleware('auth:api', [
            'except' => ['login']
        ]);

        $this->successResponse = $successResponse;
        $this->userService = $userService;
    }

    /**
     * ログインする。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request): JsonResponse
    {
        $errorMsgs = [
            'email' => 'メールアドレスの形式が正しくありません。',
        ];

        Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ], $errorMsgs)->validate();

        $token = $this->userService->login($request->email, $request->password);

        if (!$token) {
            throw new AuthenticationException();
        }

        return $this->responseToken($token);
    }

    /**
     * ログアウトする。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->userService->logout();

        $this->successResponse->setContent([
            'result' => true
        ]);

        return $this->successResponse->send();
    }

    /**
     * トークンをリフレッシュする。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $refreshedToken = $this->userService->refresh();

        return $this->responseToken($refreshedToken);
    }

    /**
     * トークンを含んだレスポンスを返却する。
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    private function responseToken(string $token): JsonResponse
    {
        $this->successResponse->setContent([
            'access_token'  => $token,
        ]);

        return $this->successResponse->send();
    }
}
