<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as Mail_send;
use App\Models\OurCompany;
use App\Models\application;
use App\Models\message;
use App\Models\history;
use App\Models\attachment;
use App\Models\address;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class MailController extends Controller
{
    public function mail_sent(Request $request)
    {
        $application = application::findOrFail($request->id_application);
        $data["email"] = $request->to;
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                $files[] = public_path('/storage/files/' . $request->id_application . '/' . $request->location[$i]);
            }
        $messages = new message();
        $messages->mailbox_id = $application->our_company;
        $messages->type = 1;
        $messages->application_id = $request->id_application;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files, $messages) {
            foreach ($data["email"] as $email) {
                $message->to($email, $data["name"]);
                $address = new address;
                $address->message_id = $messages->id;
                $address->type = 'to';
                $address->email = $email;
                $address->save();
            }

            $message->from($data['from'], $data['name'])
                ->subject($data["title"]);
            foreach ($files as $file) {
                // $attach = new attachment;
                // $attach->message_id = $message->id;
                // $attach->location = basename($file);
                $message->attach($file);
            }
        });
        // $application->id_status = 25;
        $history = new history;
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = $data["from"];
        $address->save();
        $history->id_user = $request->user_id;
        $history->id_application = $request->id_application;
        $history->id_status = 25;
        $history->save();
        $application->save();
        return $request;
    }
}
