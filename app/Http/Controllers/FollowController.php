<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FollowController extends Controller
{
    //you cannnot follow yourself
    public function createFollow(User $user){
if($user->id==auth()->user()->id){
    return back()->with('error','You cannot follow yourserlf');
}

//you can't follow someone if you already follow
$existCheck=Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->count();   

if($existCheck){
    return back()->with('error','you are already following that user');
}

$newFollow=new Follow;
$newFollow->user_id=auth()->user()->id;
$newFollow->followeduser=$user->id;
$newFollow->save();

return back()->with('success','you sucessfully followed');
    }

    public function removeFollow(User $user){
Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->delete();
  return back()->with('success','You successfully unfollowed');
    }

}
