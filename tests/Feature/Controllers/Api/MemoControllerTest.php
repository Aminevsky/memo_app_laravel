<?php

namespace Tests\Feature\Controllers\Api;

use App\Services\MemoCreateService;
use App\Services\MemoDeleteService;
use App\Services\MemoUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class MemoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, ResponseAssertTrait;

    /** @var string テーブル名（メモ情報） */
    const TABLE_NAME_MEMO = 'memos';

    /** @var int タイトル最大文字数 */
    const TITLE_MAX_LENGTH = 255;

    /** @var int 本文最大文字数 */
    const BODY_MAX_LENGTH = 5000;

    /***************************************************************
     * 共通
     ***************************************************************/
    /**
     * DBにテストユーザを作成する。
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private function makeUser()
    {
        return factory(\App\User::class)->create();
    }

    /***************************************************************
     * store()
     ***************************************************************/
    /**
     * @test
     */
    public function メモを新規作成すること()
    {
        $title = 'タイトルテスト';
        $body = '本文テスト';
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->postJson(route('memos.store'), [
                'title' => $title,
                'body'  => $body,
            ]);

        $this->assertSuccessResponse($response);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);
        $response->assertJsonFragment([
            'title' => $title,
            'body'  => $body,
        ]);
    }

    /**
     * @test
     */
    public function DB追加に失敗した場合にエラーメッセージが返却されること()
    {
        // サービスクラスをモック化してエラー状態を発生させる。
        $mockService = Mockery::mock(MemoCreateService::class);
        $mockService->shouldReceive('create')
            ->andReturn(false);
        app()->instance(MemoCreateService::class, $mockService);

        $title = 'タイトルテスト';
        $body = '本文テスト';
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->postJson(route('memos.store'), [
                'title' => $title,
                'body'  => $body,
            ]);

        $this->assertServerErrorResponse($response, 'メモの新規作成に失敗しました。');

        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body'  => $body,
        ]);
    }

    /**
     * @test
     * @dataProvider dataProviderForStoreValidationFail
     * @param string $title
     * @param string $body
     * @param array $errors
     */
    public function 新規作成時にバリデーションエラーが発生すること(string $title, string $body, array $errors)
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->postJson(route('memos.store'), [
                'title' => $title,
                'body'  => $body,
            ]);

        $this->assertValidationErrorResponse($response, $errors);

        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body'  => $body,
        ]);
    }

    /**
     * データプロバイダ（バリデーションエラー）
     *
     * @return array[]
     */
    public function dataProviderForStoreValidationFail()
    {
        return [
            'タイトル：空文字' => [
                '',
                '本文テスト',
                [
                    [
                        'name' => 'title',
                        'detail' => ['タイトルを指定してください。'],
                    ],
                ],
            ],
            'タイトル：文字数超過' => [
                str_repeat('a', self::TITLE_MAX_LENGTH + 1),
                '本文テスト',
                [
                    [
                        'name' => 'title',
                        'detail' => ['タイトルは' . self::TITLE_MAX_LENGTH . '文字以下で指定してください。'],
                    ],
                ],
            ],
            '本文：空文字' => [
                'タイトルテスト',
                '',
                [
                    [
                        'name' => 'body',
                        'detail' => ['本文を指定してください。'],
                    ],
                ],
            ],
            '本文：文字数超過' => [
                'タイトルテスト',
                str_repeat('a', self::BODY_MAX_LENGTH + 1),
                [
                    [
                        'name' => 'body',
                        'detail' => ['本文は' . self::BODY_MAX_LENGTH . '文字以下で指定してください。']
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderForStoreValidationSuccess
     * @param string $title
     * @param string $body
     */
    public function 新規作成時にバリデーションエラーが発生しないこと(string $title, string $body)
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->postJson(route('memos.store'), [
                'title' => $title,
                'body'  => $body,
            ]);

        $this->assertSuccessResponse($response);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body'  => $body,
        ]);
    }

    /**
     * データプロバイダ（バリデーション正常）
     *
     * @return array[]
     */
    public function dataProviderForStoreValidationSuccess()
    {
        return [
            'タイトル：最大文字数' => [
                str_repeat('a', self::TITLE_MAX_LENGTH),
                '本文テスト'
            ],
            '本文：最大文字数' => [
                'タイトルテスト',
                str_repeat('a', self::BODY_MAX_LENGTH),
            ]
        ];
    }

    /***************************************************************
     * show()
     ***************************************************************/
    /**
     * @test
     */
    public function メモを取得できること()
    {
        $id = 1;
        factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->getJson(route('memos.show', ['memo' => (string)$id]));

        $this->assertSuccessResponse($response);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);
        $response->assertJsonFragment(['id' => $id]);
    }

    /**
     * @test
     */
    public function メモが存在しない場合にエラーが発生すること()
    {
        $id = 1;

        // 存在する場合に備えて削除しておく。
        \App\Memo::destroy($id);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->getJson(route('memos.show', ['memo' => (string)$id]));

        $errorMsg = 'メモが存在しません。';
        $this->assertClientErrorResponse($response, Response::HTTP_NOT_FOUND, $errorMsg);
    }

    /***************************************************************
     * update()
     ***************************************************************/
    /**
     * @test
     */
    public function メモのタイトルを更新できること()
    {
        $id = 1;
        $afterTitle = str_repeat('a', self::TITLE_MAX_LENGTH);

        factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]), [
                'title' => $afterTitle,
            ]);

        $this->assertSuccessResponse($response);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'title' => $afterTitle,
        ]);
    }

    /**
     * @test
     */
    public function メモの本文を更新できること()
    {
        $id = 1;
        $afterBody = str_repeat('a', self::BODY_MAX_LENGTH);

        factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]), [
                'body' => $afterBody,
            ]);

        $this->assertSuccessResponse($response);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'body'  => $afterBody,
        ]);
    }

    /**
     * @test
     */
    public function 複数項目を同時に更新できること()
    {
        $id = 1;
        $afterTitle = 'タイトル_更新後';
        $afterBody = '本文_更新後';

        factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]), [
                'title' => $afterTitle,
                'body'  => $afterBody,
            ]);

        $this->assertSuccessResponse($response);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'title' => $afterTitle,
            'body'  => $afterBody,
        ]);
    }

    /**
     * @test
     */
    public function 更新項目が1つも指定されていない場合にエラーとなること()
    {
        $id = 1;

        $model = factory(\App\Memo::class)->make([
            'id'    => $id,
        ]);
        $title = $model->title;
        $body = $model->body;
        $model->save();
        $createdAt = $model->created_at;
        $updatedAt = $model->updated_at;

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]));

        $errors = [
            [
                'name' => 'title',
                'detail' => ['タイトルまたは本文のいずれかを指定してください。'],
            ],
            [
                'name' => 'body',
                'detail' => ['タイトルまたは本文のいずれかを指定してください。'],
            ],
        ];
        $this->assertValidationErrorResponse($response, $errors);

        // レコードが更新されていないこと
        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'title' => $title,
            'body'  => $body,
            'created_at'    => $createdAt,
            'updated_at'    => $updatedAt,
        ]);
    }

    /**
     * @test
     * @dataProvider dataProviderForUpdateValidationFail
     * @param array $params
     * @param array $errors
     */
    public function 更新時にバリデーションエラーが発生すること(array $params, array $errors)
    {
        $id = 1;
        $beforeModel = factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]), $params);

        $this->assertValidationErrorResponse($response, $errors);

        // DBが更新されていないこと
        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'title' => $beforeModel->title,
            'body'  => $beforeModel->body,
            'created_at' => $beforeModel->created_at,
            'updated_at' => $beforeModel->updated_at,
        ]);
    }

    /**
     * データプロバイダ（バリデーションエラー）
     *
     * @return array[]
     */
    public function dataProviderForUpdateValidationFail()
    {
        return [
            'タイトル：文字数超過' => [
                [
                    'title' => str_repeat('a', self::TITLE_MAX_LENGTH + 1),
                ],
                [
                    [
                        'name' => 'title',
                        'detail' => ['タイトルは' . self::TITLE_MAX_LENGTH . '文字以下で指定してください。'],
                    ],
                ],
            ],
            '本文：文字数超過' => [
                [
                    'body' => str_repeat('a', self::BODY_MAX_LENGTH + 1)
                ],
                [
                    [
                        'name' => 'body',
                        'detail' => ['本文は' . self::BODY_MAX_LENGTH . '文字以下で指定してください。']
                    ],
                ],
            ]
        ];
    }

    /**
     * @test
     */
    public function DB更新に失敗した場合にエラーが発生すること()
    {
        $id = 1;

        // サービスクラスをモック化してエラー状態を発生させる。
        $mockService = Mockery::mock(MemoUpdateService::class);
        $mockService->shouldReceive('update')
            ->withAnyArgs()
            ->andReturn(null);
        app()->instance(MemoUpdateService::class, $mockService);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->putJson(route('memos.update', ['memo' => $id]), [
                'title' => 'タイトル_更新後',
            ]);

        $this->assertServerErrorResponse($response, 'メモの更新に失敗しました。');
    }

    /***************************************************************
     * destroy()
     ***************************************************************/
    /**
     * @test
     */
    public function メモを削除できること()
    {
        $id = 1;

        factory(\App\Memo::class)->create(['id' => $id]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->deleteJson(route('memos.destroy', ['memo' => $id]));

        $this->assertSuccessResponse($response);
        $response->assertExactJson(['result' => true]);

        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'id' => $id
        ]);
    }

    /**
     * @test
     */
    public function DB削除に失敗した場合にエラーが発生すること()
    {
        $id = 1;

        $mockService = Mockery::mock(MemoDeleteService::class);
        $mockService->shouldReceive('delete')
            ->with($id)
            ->andReturn(false);
        app()->instance(MemoDeleteService::class, $mockService);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->deleteJson(route('memos.destroy', ['memo' => $id]));

        $this->assertServerErrorResponse($response, 'メモの削除に失敗しました。');
    }

    /***************************************************************
     * index()
     ***************************************************************/
    /**
     * @test
     */
    public function メモを全件取得できること()
    {
        $recordAmount = 2;

        factory(\App\Memo::class, $recordAmount)->create();

        $user = $this->makeUser();

        $response = $this->actingAs($user)->getJson(route('memos.index'));

        $this->assertSuccessResponse($response);
        $response->assertJsonCount($recordAmount);
    }

    /**
     * @test
     */
    public function メモが存在しない場合は空のJSONが返ること()
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->getJson(route('memos.index'));

        $this->assertSuccessResponse($response);
        $response->assertJsonCount(0);
    }
}
