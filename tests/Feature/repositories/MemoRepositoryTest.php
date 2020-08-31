<?php

namespace Tests\Feature\repositories;

use App\Http\Repositories\MemoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemoRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var string テーブル名（メモ情報） */
    const TABLE_NAME_MEMO = 'memos';

    /**
     * @test
     */
    public function 新規作成できること()
    {
        $title = $this->faker->text(255);
        $body = $this->faker->text(1000);

        $repository = new MemoRepository();
        $repository->create($title, $body);

        $this->assertDatabaseHas(self::TABLE_NAME_MEMO, [
            'title' => $title,
            'body' => $body,
        ]);
    }
}
