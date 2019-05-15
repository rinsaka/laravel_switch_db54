<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About this project

Laravel のデータベースを sqlite と MySQL で切り替えられるように．特に MySQL では Mroonga ストレージエンジンを利用しているので，同じマイグレーションファイルでは sqlite の環境ではデータベースを作成できない．このプロジェクトは .env の環境変数を取り出してマイグレーションを調整するサンプルです．

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).



# データベースを切り替えても動くように
## MySQL と sqlite を .env で切り替える

### まずは，sqlite で

- .env を編集

~~~
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=homestead
# DB_USERNAME=homestead
# DB_PASSWORD=secret
~~~

- データベースを作成
~~~
touch database/database.sqlite
~~~

- 不要なマイグレーションファイルを削除

~~~
rm 2014_10_12_000000_create_users_table.php
rm 2014_10_12_100000_create_password_resets_table.php
~~~

- 適当なマイグレーションファイルを生成

~~~
php artisan make:migration create_comments_table --create=comments
~~~

- マイグレーションの内容

~~~
public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->increments('id');
        $table->string('comment');
        $table->timestamps();
    });
}
~~~

- マイグレーション

~~~
php artisan migrate
~~~

- シーダーの設置

~~~
php artisan make:seeder CommentsTableSeeder
php ../composer.phar dump-autoload
~~~

- DatabaseSeeder の内容

~~~
public function run()
{
  // $this->call(UsersTableSeeder::class);
  $this->call(CommentsTableSeeder::class);
}
~~~

- CommentsTableSeeder の内容

~~~
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
~~~

- Comment モデルと Comments コントローラを作る

~~~
php artisan make:model Comment
php artisan make:controller CommentsController
~~~

- route を書く

~~~
Route::get('comments', 'CommentsController@index');
~~~

- CommentsController

~~~
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentsController extends Controller
{
  public function index()
  {
    $comments = Comment::get();
    dd($comments);
  }
}
~~~

- server を起動する

~~~
php artisan serve --host 192.168.33.100 --port 8000
~~~

- 動作確認
~~~
http://192.168.33.100:8000/comments
~~~


### 次に MySQL で動くように

- MySQL のデータベースを作成する

~~~
mysql -u root -p
パスワードを入力
CREATE DATABASE homestead;
GRANT ALL on homestead.* TO homestead@localhost IDENTIFIED BY 'Secret.2018';
exit
~~~

- マイグレーションを実行

~~~
php artisan migrate:status
php artisan migrate
php artisan migrate:status
php artisan db:seed
~~~

- server を起動する

~~~
php artisan serve --host 192.168.33.100 --port 8000
~~~

- 動作確認

~~~
http://192.168.33.100:8000/comments
~~~

### MySQL 特有の機能を利用する

- マイグレーションファイルを修正

~~~
public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->increments('id');
        $table->string('comment');
        $table->timestamps();

        $table->engine = 'Mroonga';
    });

    // ストレージエンジンをMroongaのラッパーモードに変更する
    DB::statement("ALTER TABLE comments engine=Mroonga COMMENT ='engine \"InnoDB\"' DEFAULT CHARSET=utf8");

    // フルテキストインデックスを追加
    DB::statement('ALTER TABLE comments ADD FULLTEXT index_comment_on_comments(`comment`)');
}
~~~

- マイグレーションを再度実行

~~~
php artisan migrate:status
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
~~~

- これでもよい

~~~
php artisan migrate:rollback; php artisan migrate; php artisan db:seed
~~~

- 動作確認
MySQLでMroongaのインデックスが使えるようになった．ただし，.env で sqlite にすると，マイグレーションでエラーになる．これは想定通り．

### .env の内容を取り出して処理する

- マイグレーションファイルを変更する

~~~
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
~~~


- CommentsController でも接続先データベースを表示する

~~~
public function index()
{
  $comments = Comment::get();
  dd(env("DB_CONNECTION"), $comments);
}
~~~

- 動作確認
MySQLでもsqliteでもうまく動作した！なお，created_at についても，投稿時はMySQL, sqlite ともに自動的に設定された．
