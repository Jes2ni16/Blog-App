<?php

namespace App\Http\Controllers;



use App\Models\User;
use App\Models\Follow;
use App\Events\ExampleEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{

    public function storeAvatar(Request $request){
    $request->validate([
    'avatar'=>'required|image||max:3000'
    ]);

    $user =auth()->user();
    $filename=$user->id.'-'. uniqid().'.jpg';

     $imgData=  Image::make($request->file('avatar'))->fit(120)->encode('jpg');
     Storage::put('public/avatars/'.$filename,$imgData);
 
    $oldAvatar=$user->avatars;

     $user->avatars=$filename;
     $user->save();

     if($oldAvatar!="/fallback-avatar.jpg"){
        Storage::delete(str_replace("/storage/","public/",$oldAvatar));
     }
     return back()->with('success','congrats you have uploaded a new avatar');
    }

    public function showAvatar(){
        return view('avatar-form');
    }

private function getSharedData($user){
    $currentlyFollowing =0;
    if(auth()->check()){
        $currentlyFollowing= Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->count();
    }

    View::share('sharedData',['currentlyFollowing'=>$currentlyFollowing,'avatars'=>$user->avatars,'username' => $user->username, 'postCount'=>$user->posts()->count(),'followerCount'=>$user->followers()->count(),'followingCount'=>$user->following()->count()]);
}

        public function profile(User $user){
            $this->getSharedData($user);
    return view('profile-post',['posts'=> $user->posts()->latest()->get()]);
        }

    public function getFollowers(User $user){
        $this->getSharedData($user);
        return view('profile-followers',['followers'=> $user->followers()->latest()->get()]);
    }

    public function getFollowing(User $user){
        $this->getSharedData($user);
        return view('profile-following',['following'=> $user->following()->latest()->get()]);
    }

    public function admin(){ 
 
  return 'yess!!!!!';
    }

    public function logout(){
        event(new ExampleEvent(['username'=>auth()->user()->username,'action'=>'logout']));
        auth()->logout();
       

        return redirect('/');
    }

    public function showCorrectHomepage(){
        if( auth()->check()){
            return view('homepage-feed',['posts'=>auth()->user()->feedPosts()->latest()->paginate(4)]);
        }
        else{
            return view('homepage');
        }
    }

    public function login(Request $request){
    $incomingFields=$request->validate([
        'loginusername'=> 'required',
        'loginpassword'=> 'required',
    ]);

    if(auth()->attempt(['username'=>$incomingFields['loginusername'],'password'=>$incomingFields['loginpassword']])){
    $request->session()->regenerate();
    event(new ExampleEvent(['username'=>auth()->user()->username,'action'=>'login']));
        return redirect('/')->with('success','you have log in bobot');
    }
    else{
        return redirect('/')->with('error','Invalid Password or Username');

    }
    }
    public function register(Request $request){
        $incomingFields=$request->validate([
            'username'=>['required','max:20',Rule::unique('users','username')],
            'email'=>['required',Rule::unique('users','email')],
            'password' => ['required']
        ]);
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank you for creating an account.');
    }

}
