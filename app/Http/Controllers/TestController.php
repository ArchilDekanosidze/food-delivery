<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\SendSms;
use App\Jobs\SendEmail;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testEmail()
    {
        $user = User::find(1);
        $mailable = new UserRegistered();
        SendEmail::dispatch($user, $mailable);
    }

    public function testSms()
    {
        $user = User::find(1);
        $text = '123';
        SendSms::dispatch($user, $text);
    }

}
