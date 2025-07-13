<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index()
    {
        Mail::to("vnsmrkdlwn@gmail.com")->send(new TestMail());

        dd('Mail sent successfully!');
    }
}
