<?php

namespace Tests\Feature\Controllers\Api;

use App\Services\MemoCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class MemoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var string テーブル名（メモ情報） */
    const TABLE_NAME_MEMO = 'memos';

    /** @var int タイトル最大文字数 */
    const TITLE_MAX_LENGTH = 255;

    /** @var int 本文最大文字数 */
    const BODY_MAX_LENGTH = 5000;

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

        $response = $this->postJson(route('memos.store'), [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertStatus(Response::HTTP_OK);

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

        $response = $this->postJson(route('memos.store'), [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertExactJson([
            'error' => '作成に失敗しました。',
        ]);
    }

    /**
     * @test
     * @dataProvider dataProviderForStoreValidationFail
     * @param string $title
     * @param string $body
     * @param array $errors
     */
    public function バリデーションエラーが発生すること(string $title, string $body, array $errors)
    {
        $response = $this->postJson(route('memos.store'), [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment($errors);

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
                    'title' => ['タイトルを指定してください。']
                ]
            ],
            'タイトル：文字数超過' => [
                str_repeat('a', self::TITLE_MAX_LENGTH + 1),
                '本文テスト',
                [
                    'title' => ['タイトルは' . self::TITLE_MAX_LENGTH . '文字以下で指定してください。']
                ]
            ],
            '本文：空文字' => [
                'タイトルテスト',
                '',
                [
                    'body' => ['本文を指定してください。']
                ]
            ],
            '本文：文字数超過' => [
                'タイトルテスト',
                str_repeat('a', self::BODY_MAX_LENGTH + 1),
                [
                    'body' => ['本文は' . self::BODY_MAX_LENGTH . '文字以下で指定してください。']
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderForStoreValidationSuccess
     * @param string $title
     * @param string $body
     */
    public function バリデーションエラーが発生しないこと(string $title, string $body)
    {
        $response = $this->postJson(route('memos.store'), [
            'title' => $title,
            'body'  => $body,
        ]);

        $response->assertStatus(Response::HTTP_OK);

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

        $response = $this->getJson(route('memos.show', ['memo' => (string)$id]));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);
        $response->assertJsonFragment(['id' => $id]);
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
        $afterTitle = 'タイトル_更新後';

        factory(\App\Memo::class)->create(['id' => $id]);

        $response = $this->putJson(route('memos.update', ['memo' => $id]), [
            'title' => $afterTitle,
        ]);

        $response->assertStatus(Response::HTTP_OK);

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
        $afterBody = '本文_更新後';

        factory(\App\Memo::class)->create(['id' => $id]);

        $response = $this->putJson(route('memos.update', ['memo' => $id]), [
            'body' => $afterBody,
        ]);

        $response->assertStatus(Response::HTTP_OK);

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

        $response = $this->putJson(route('memos.update', ['memo' => $id]), [
            'title' => $afterTitle,
            'body'  => $afterBody,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'id'    => $id,
            'title' => $afterTitle,
            'body'  => $afterBody,
        ]);
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

        $response = $this->deleteJson(route('memos.destroy', ['memo' => $id]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson(['result' => true]);

        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'id' => $id
        ]);
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

        $response = $this->getJson(route('memos.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount($recordAmount);
    }

    /**
     * @test
     */
    public function メモが存在しない場合は空のJSONが返ること()
    {
        $response = $this->getJson(route('memos.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0);
    }
}
