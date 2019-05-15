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
            <li>{{ $comment->comment }} {{ $comment->created_at }}</li>
        @endforeach
    </ul>
</body>
</html>
