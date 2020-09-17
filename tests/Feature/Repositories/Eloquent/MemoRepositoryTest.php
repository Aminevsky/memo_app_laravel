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

    /**
     * @test
     */
    public function 更新項目が空の場合は例外が発生すること()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('更新項目を指定してください。');

        $repository = new MemoRepository();
        $repository->update(1, []);
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
     * fetchAllByUserId()
     ***************************************************************/
    /**
     * @test
     */
    public function ユーザIDをキーにメモを全件取得して配列で返却すること()
    {
        $validUserId = self::TEST_USER_ID;
        $invalidUserId = $validUserId + 1;

        // 取得されないデータのユーザを作成する。
        factory(\App\User::class)->create([
            'id' => $invalidUserId,
        ]);

        factory(\App\Memo::class)->createMany([
            ['user_id' => $validUserId],
            ['user_id' => $invalidUserId], // 取得されるべきでないデータ
            ['user_id' => $validUserId],
        ]);

        $repository = new MemoRepository();
        $resultItems = $repository->fetchAllByUserId(self::TEST_USER_ID);

        $this->assertIsArray($resultItems);
        $this->assertCount(2, $resultItems);

        foreach ($resultItems as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('body', $item);
            $this->assertArrayHasKey('created_at', $item);
            $this->assertArrayHasKey('updated_at', $item);
            $this->assertNotSame($invalidUserId, $item['user_id']);
        }
    }

    /**
     * @test
     */
    public function メモが存在しない場合に空配列を返却すること()
    {
        $repository = new MemoRepository();
        $result = $repository->fetchAllByUserId(self::TEST_USER_ID);

        $this->assertIsArray($result);
        $this->assertSame(0, count($result));
    }

    /***************************************************************
     * fetchUserId()
     ***************************************************************/
    /**
     * @test
     */
    public function ユーザID取得成功時にユーザIDを返却すること()
    {
        $memoId = 100;

        factory(\App\Memo::class)->create([
            'id' => $memoId,
            'user_id' => self::TEST_USER_ID,
        ]);

        $repository = new MemoRepository();
        $result = $repository->fetchUserId($memoId);

        $this->assertSame(self::TEST_USER_ID, $result);
    }

    /**
     * @test
     */
    public function ユーザID取得失敗時にnullを返却すること()
    {
        $repository = new MemoRepository();
        $result = $repository->fetchUserId(1);

        $this->assertNull($result);
    }
}
