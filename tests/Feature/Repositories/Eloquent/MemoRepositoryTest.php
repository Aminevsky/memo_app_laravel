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

    /** @var int ユーザID */
    const TEST_USER_ID = 1;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        // テスト用ユーザを作成する。
        factory(\App\User::class)->create([
            'id' => self::TEST_USER_ID,
        ]);
    }

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
        $userId = 1;

        $repository = new MemoRepository();
        $result = $repository->create($title, $body, $userId);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body' => $body,
            'user_id' => $userId,
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

    /***************************************************************
     * update()
     ***************************************************************/
    /**
     * @test
     */
    public function タイトルを更新できること()
    {
        $id = 1;
        $afterTitle = 'タイトル_更新後';

        factory(\App\Memo::class)->create(['id' => $id]);

        $repository = new MemoRepository();
        $contents['title'] = $afterTitle;
        $result = $repository->update($id, $contents);

        $this->assertIsArray($result);
        $this->assertSame($id, $result['id']);
        $this->assertSame($afterTitle, $result['title']);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    /**
     * @test
     */
    public function 本文を更新できること()
    {
        $id = 1;
        $afterBody = '本文_更新後';

        factory(\App\Memo::class)->create(['id' => $id]);

        $repository = new MemoRepository();
        $contents['body'] = $afterBody;
        $result = $repository->update($id, $contents);

        $this->assertIsArray($result);
        $this->assertSame($id, $result['id']);
        $this->assertSame($afterBody, $result['body']);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
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

        $repository = new MemoRepository();
        $contents = [
            'title' => $afterTitle,
            'body'  => $afterBody,
        ];
        $result = $repository->update($id, $contents);

        $this->assertIsArray($result);
        $this->assertSame($id, $result['id']);
        $this->assertSame($afterTitle, $result['title']);
        $this->assertSame($afterBody, $result['body']);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
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

        $repository = new MemoRepository();
        $result = $repository->delete($id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing(self::TABLE_NAME_MEMO, [
            'id' => $id,
        ]);
    }

    /***************************************************************
     * fetchAll()
     ***************************************************************/
    /**
     * @test
     */
    public function メモを全件取得して配列で返却すること()
    {
        $recordAmount = 2;

        factory(\App\Memo::class, $recordAmount)->create();

        $repository = new MemoRepository();
        $result = $repository->fetchAll();

        $this->assertIsArray($result);
        $this->assertSame($recordAmount, count($result));

        for ($i = 0; $i < $recordAmount; $i++) {
            $this->assertArrayHasKey('id', $result[$i]);
            $this->assertArrayHasKey('title', $result[$i]);
            $this->assertArrayHasKey('body', $result[$i]);
            $this->assertArrayHasKey('created_at', $result[$i]);
            $this->assertArrayHasKey('updated_at', $result[$i]);
        }
    }

    /**
     * @test
     */
    public function メモが存在しない場合に空配列を返却すること()
    {
        $repository = new MemoRepository();
        $result = $repository->fetchAll();

        $this->assertIsArray($result);
        $this->assertSame(0, count($result));
    }

    /***************************************************************
     * fetchUserId()
     ***************************************************************/
    /**
     *
     */
    public function メモに紐づくユーザIDを返却すること()
    {
    }
}
