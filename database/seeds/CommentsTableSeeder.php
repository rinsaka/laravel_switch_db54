<?php

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // 一旦中身を削除する
    DB::table('comments')->delete();

    DB::table('comments')->insert([
        'comment' => '最初のコメント'
    ]);

    DB::table('comments')->insert([
        'comment' => 'あいうえお'
    ]);

    DB::table('comments')->insert([
        'comment' => 'かきくけこ'
    ]);
  }
}
