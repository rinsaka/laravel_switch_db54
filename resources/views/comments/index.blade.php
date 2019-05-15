<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>コメント一覧</title>
</head>
<body>
    <p>
      接続先データベース： {{ $connection }}
    </p>
    <h1>コメント一覧</h1>
    <ul>
        @foreach ($comments as $comment)
            <li>{{ $comment->comment }} ({{ $comment->created_at }})</li>
        @endforeach
    </ul>

    <h1>コメント投稿</h1>
    <div>
        <form method="post" action="{{ url('/comments') }}">
            {{ csrf_field() }}
            <p>
                <label for="comment">Comment: </label>
                <input type="text" name="comment" id="comment" value="">
                @if ($errors->has('comment'))
                  <span class="error">{{ $errors->first('comment') }}</span>
                @endif
            </p>
            <p>
                <input type="submit" value="投稿">
            </p>
        </form>
    </div>
</body>
</html>
