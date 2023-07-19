<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends  Controller
{

    public function homepage(){

$ourName='Brad';
$animals=['errng','erro','bitin'];

        return view('homepage',['allAnimals'=>$animals,'name'=>$ourName,'catname'=>'tonini']);
    }

    public function aboutPage(){
        return view('single-post');
    }
}
