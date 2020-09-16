<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToMemosTable extends Migration
{
    /** @var string */
    const ADD_COLUMN_NAME = 'user_id';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->unsignedBigInteger(self::ADD_COLUMN_NAME);

            $table->foreign(self::ADD_COLUMN_NAME)
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->dropForeign([self::ADD_COLUMN_NAME]);

            $table->dropColumn(self::ADD_COLUMN_NAME);
        });
    }
}
