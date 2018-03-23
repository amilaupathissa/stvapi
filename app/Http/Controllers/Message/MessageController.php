<?php

namespace App\Http\Controllers\Message;

use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Message as MessageResource;

class MessageController extends Controller
{
    public function mailSend(Request $request)
    {
        $data = new MessageResource($request);
        $name = $data->name;
        $email = $data->email;
        $text = $data->text;
        $subject = $data->subject;

        $request->validate([
            'name' => 'required|max:75|string',
            'email' => 'required|email',
            'subject' => 'required|max:100',
            'text' => 'required'
        ]
        // ,
        // [
        //     'name.required' => 'නම ඇතුලත් කිරීම අනිවාර්ය වේ.',
        //     'name.max' => 'නම උපරිමය අකුරු 75 කට සීමා කිරීමට කාරුණික ව්න්න.',
        //     'name.string' => 'ඇතුලත් කර ඇති නම වලංගු නොවේ.',
        //     'email.required' => 'ඊමේල් ලිපිනය ඇතුලත් කිරීම අනිවාර්ය වේ.',
        //     'email.email' => 'ඇතුලත් කර ඇති ඊමේල් ලිපිනය වලංගු නොවේ.',
        //     'subject.required' => 'මාතෘකාව අනිවාර්ය වේ.',
        //     'subject.max' => 'මාතෘකාව උපරිමය අකුරු 100 කට සීමා කිරීමට කාරුණික ව්න්න.',
        //     'text.required' => 'ඔබගේ පණිවුඩය ඇතුලත් කිරීමට කාරුණික වන්න.'
        // ]
    );


        return "ok.. is has been testing";
        

        return Mail::send(['text'=>'email'],['name'=>$name,"subject"=>$subject,'email'=> $email,'text'=> $text],function($message) use ($subject,$name){
            $message->to('web.shraddha@gmail.com', 'to backend')
                    ->subject($subject);
            $message->from('mobile.app.shraddha@gmail.com','Message from: '.$name);
        });
        // return "sent email";
    }
    
}
