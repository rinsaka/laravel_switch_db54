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
}
