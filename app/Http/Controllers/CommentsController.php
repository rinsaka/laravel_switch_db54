<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentsController extends Controller
{
  public function index()
  {
    $comments = Comment::get();
    $connection = env("DB_CONNECTION");
    return view('comments.index')
            ->with('comments', $comments)
            ->with('connection', $connection);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'comment' => 'required|max:140'
    ]);

    $comment = new Comment();
    $comment->comment = $request->comment;
    $comment->save();
    return redirect('/comments');
  }
}
