<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

public function search($term){
    $posts = Post::search($term)->get();
    $posts->load('user:id,username,avatar');
    return $posts;
}

public function actuallyUpdated(Post $post ,Request $request){
    $incomingFields=$request->validate([
        'title'=>'required',
        'body'=>'required'
    ]);
    $incomingFields['title']=strip_tags($incomingFields['title']);
    $incomingFields['body']=strip_tags($incomingFields['body']);

$post->update($incomingFields);

return back()->with('success','Post successfuly updated');
}

public function showEditForm(Post $post){
    return view('edit-post',['post'=>$post]);
}


public function delete(Post $post){
 $post->delete();
 return redirect('/profile/'. auth()->user()->username)->with('success','Post succesfully deleeted');
}

   
public function viewSinglePost(Post $post){
    $ourHTML=strip_tags(Str::markdown($post->body),'<p><<ul><ol><strong>');
    $post['body']=$ourHTML;
    return view('single-post',['post'=>$post]);
}


public function storeNewPost(Request $request){
    $incomingFields=$request->validate([
        'title'=>'required',
        'body'=>'required'
    ]);

    $incomingFields['title']=strip_tags($incomingFields['title']);
    $incomingFields['body']=strip_tags($incomingFields['body']);
$incomingFields['user_id']=auth()->id();

$newPost= Post::create($incomingFields);
    return redirect("/post/{$newPost->id}")->with('success','New post successfully created Thank you');
}

    public function showCreateForm(){
        return view('create-post');
    }
}
