<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Comment;
use DB;

class PostCommentsController extends Controller
{


    public function store(Post $post)
    {

        request()->validate([
            'body' => 'required'
        ]);

        $post->comments()->create([
            'user_id' => request()->user()->id,
            'parent_id' => request()->get('comment_id'),
            'body' => request('body')
        ]);

        return back();

    }

    public function destroy($id)
    {

        Comment::where('id',$id)->delete();

        return back();
    }

}
