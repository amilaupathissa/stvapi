<?php

namespace App\Http\Controllers\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Message as MessageResource;
use Mail;

class MessageController extends Controller
{
    public function mailSend(Request $request)
    {
        $data = new MessageResource($request);
        $name = $data->name;
        $email = $data->email;
        $text = $data->text;
        $subject = $data->subject;
        

        return Mail::send(['text'=>'email'],['name'=>$name,"subject"=>$subject,'email'=> $email,'text'=> $text],function($message) use ($subject,$name){
            $message->to('web.shraddha@gmail.com', 'to backend')
                    ->subject($subject);
            $message->from('mobile.app.shraddha@gmail.com','Message from: '.$name);
        });
        // return "sent email";
    }
    
}
