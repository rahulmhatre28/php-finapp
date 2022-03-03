<?php

namespace App\Traits;
use Mail;

trait Mailer {
    
    protected function sendMail($to,$subject,$body) {
        $data = array('name'=>"Droid Finserve");
        try 
        {
            Mail::send(['text'=>'mail'], $data, function($message) use($to,$body,$subject) {
                $message->to($to)->subject
                   ($subject);
                $message->body($body);
                $message->from(env('MAIL_SENDER'),'Droid Finserve');
            });  
            return ['status'=>true];
        }
        catch(\Exception $e){
            return ['status'=>false,'error'=>$e->getMessage()];
        } 
    }
} 

?>