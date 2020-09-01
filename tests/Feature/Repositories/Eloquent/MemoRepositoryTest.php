<?php

namespace Tests\Feature\Repositories\Eloquent;

use App\Repositories\Eloquent\MemoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemoRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var string テーブル名（メモ情報） */
    const TABLE_NAME_MEMO = 'memos';

    /***************************************************************
     * create()
     ***************************************************************/
    /**
     * @test
     */
    public function 新規作成に成功した場合は配列を返却すること()
    {
        $title = $this->faker->text(255);
        $body = $this->faker->text(1000);

        $repository = new MemoRepository();
        $result = $repository->create($title, $body);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body' => $body,
        ]);

        $this->assertNotFalse($result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($title, $result['title']);
        $this->assertSame($body, $result['body']);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    /***************************************************************
     * fetchById()
     ***************************************************************/
    /**
     * @test
     */
    public function IDで指定されたメモ情報の配列を返却すること()
    {
        $id = 1;

        factory(\App\Memo::class)->create([
            'id' => $id,
        ]);

        $repository = new MemoRepository();
        $result = $repository->fetchById($id);

        $this->assertIsArray($result);
        $this->assertSame($id, $result['id']);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    /**
     * @test
     */
    public function IDで指定されたメモ情報が存在しない場合にはnullを返却すること()
    {
        $repository = new MemoRepository();
        $result = $repository->fetchById(1);

        $this->assertNull($result);
    }
}
