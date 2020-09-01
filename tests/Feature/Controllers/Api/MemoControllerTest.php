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

        $response = $this->post(route('memos.store'), [
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

        $response = $this->post(route('memos.store'), [
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

        $response = $this->get(route('memos.show', ['memo' => (string)$id]));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'id', 'title', 'body', 'created_at', 'updated_at'
        ]);
        $response->assertJsonFragment(['id' => $id]);
    }
}
