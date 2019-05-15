<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('comment');
            $table->timestamps();

            if (env("DB_CONNECTION") == 'mysql') {
              $table->engine = 'Mroonga';
            }
        });

        if (env("DB_CONNECTION") == 'mysql') {
          // ストレージエンジンをMroongaのラッパーモードに変更する
          DB::statement("ALTER TABLE comments engine=Mroonga COMMENT ='engine \"InnoDB\"' DEFAULT CHARSET=utf8");

          // フルテキストインデックスを追加
          DB::statement('ALTER TABLE comments ADD FULLTEXT index_comment_on_comments(`comment`)');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
