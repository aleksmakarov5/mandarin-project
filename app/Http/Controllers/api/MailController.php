<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as Mail_send;
use App\Models\OurCompany;
use App\Models\application;
use App\Models\message;
use App\Models\book;
use App\Models\history;
use App\Models\attachment;
use App\Models\address;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MailController extends Controller
{
    public function mail_sent(Request $request)
    {
        $application = application::findOrFail($request->id_application);
        clearstatcache();
        $size = 0;
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                if (file_exists(public_path('/storage/files/' . $request->id_application . '/' . $request->location[$i])))
                    $size += filesize(
                        public_path('/storage/files/' . $request->id_application . '/' . $request->location[$i])
                    );
            }
        if ($size < 19922944) {
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

            Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
                foreach ($data["email"] as $email) {
                    $message->to($email, $data["name"]);
                }

                $message->from($data['from'], $data['name'])
                    ->subject($data["title"]);
                foreach ($files as $file) {
                    $message->attach($file);
                }
            });
            // $application->id_status = 25;
            $messages = new message();
            $messages->mailbox_id = $application->our_company;
            $messages->type = 1;
            $messages->subject = $request->subject;
            $messages->application_id = $request->id_application;
            $messages->body_html = $request->message;
            $messages->message_date = date('Y-m-d H:i:s');
            $messages->save();

            foreach ($data["email"] as $email) {

                $address = new address;
                $address->message_id = $messages->id;
                $address->type = 'to';
                $address->email = $email;
                $address->save();
            }

            if (isset($request->doc_id))
                foreach ($request->doc_id as $id) {
                    $attach_new = new attachment;
                    $attach_new->message_id = $messages->id;
                    $attach_new->id_application = $request->id_application;
                    $attach_old = attachment::where('id', '=', $id)->first();
                    $attach_new->sended = 1;
                    $attach_new->location = $attach_old->location;
                    $attach_new->mime_type = $attach_old->mime_type;
                    $attach_new->file_size = $attach_old->file_size;
                    $attach_new->file_name = $attach_old->file_name;
                    $attach_new->save();
                }

            $address = new address;


            $address->message_id = $messages->id;
            $address->type = 'from';
            $address->email = $data["from"];
            $address->save();
        } else {
            $id = $request->id_application;
            $file = '';
            $zip = 'storage/files/' . $request->id_application . '/fot.zip';
            $files = [];
            for ($i = 0; $i < count($request->location); $i++) {
                if (pathinfo('/storage/files/' . $request->id_application . '/' . $request->location[$i], PATHINFO_EXTENSION) != 'zip')
                    $files[] = public_path('/storage/files/' . $request->id_application . '/' . $request->location[$i]);
                else
                    $file = 'storage/files/' . $request->id_application . '/' . $request->location[$i];
            }
            if (!is_dir('storage/files/' . $id . '/split')) {

                mkdir('storage/files/' . $id . '/split', 0777, true);
                chmod('storage/files/' . $id . '/split', 0777);
            }
            rename($file, $zip);
            exec("zipsplit -b storage/files/" . $request->id_application . "/split -n 15728640 " . $zip);
            rename($zip, $file);
            $splites = scandir('storage/files/' . $request->id_application . '/split');
            $count = 0;
            for ($i = 1; $i < count($splites) - 1; $i++) {
                rename('storage/files/' . $request->id_application . '/split/fot' . $i . '.zip', 'storage/files/' . $request->id_application . '/' . pathinfo($file, PATHINFO_FILENAME) . '_' . $i . '.zip');
                $count = $i;
            }

            $count++;
            $data["email"] = $request->to;
            $data["title"] = $request->subject;
            $data["body"] = $request->message;
            $data['body'] = str_replace('____', "Размер вложений более 20Мб. Вложения отправлены в виде нескольких файлов. Количество писем: " . $count . "<br>Письмо 1 из " . $count . " <br>", $data['body']);

            $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
            $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
            $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;



            Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
                foreach ($data["email"] as $email) {
                    $message->to($email, $data["name"]);
                }

                $message->from($data['from'], $data['name'])
                    ->subject($data["title"]);
                foreach ($files as $file) {
                    $message->attach($file);
                }
            });
            // $application->id_status = 25;
            $messages = new message();
            $messages->mailbox_id = $application->our_company;
            $messages->type = 1;
            $messages->subject = $request->subject;
            $messages->application_id = $request->id_application;
            $messages->body_html = $data["body"];
            $messages->message_date = date('Y-m-d H:i:s');
            $messages->save();

            foreach ($data["email"] as $email) {

                $address = new address;
                $address->message_id = $messages->id;
                $address->type = 'to';
                $address->email = $email;
                $address->save();
            }

            if (isset($request->doc_id))
                foreach ($request->doc_id as $id) {
                    $attach_new = new attachment;
                    $attach_new->message_id = $messages->id;
                    $attach_new->id_application = $request->id_application;
                    $attach_old = attachment::where('id', '=', $id)->first();
                    $attach_new->sended = 1;
                    $attach_new->location = $attach_old->location;
                    $attach_new->mime_type = $attach_old->mime_type;
                    $attach_new->file_size = $attach_old->file_size;
                    $attach_new->file_name = $attach_old->file_name;
                    $attach_new->save();
                }

            $address = new address;


            $address->message_id = $messages->id;
            $address->type = 'from';
            $address->email = $data["from"];
            $address->save();
        }
        $history = new history;
        $history->id_user = $request->user_id;
        $history->id_application = $request->id_application;


        $status = $request->app_status;
        if ($status > 100 && $status < 200) {
            $application->finance_statuses_id = $status - 100;
            $application->ic_answer_date = date('Y-m-d H:i:s');
            $history->id_status = $status - 100;
            $history->is_fin = true;
        } elseif ($status == 200) {

            $application->ic_answer_date = date('Y-m-d H:i:s');
            if ($application->id_status <= 11) {

                $application->check_date = date('Y-m-d H:i:s');
                $application->id_status = 11;
            }
            $history->id_status = $status;
        } else {
            $application->id_status = $status;
            $application->check_date = date('Y-m-d H:i:s');
            $history->id_status = $status;
        }
        $application->ic_answer = false;
        $history->save();
        $application->save();
        return $request;
    }
    public function book()
    {
        $book = book::all();
        return $book;
    }
    public function addUser(Request $request)
    {
        $user = new book;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company = $request->company;
        $user->city = $request->city;
        $user->save();
        return $user;
    }
    public function addApp(Request $request)
    {
        $user = new application;
        $user->id_user = $request->user_id;
        $user->id_status = 1;
        $user->id_our_company = 1;
        $user->save();
        $user->save();
        return $user;
    }
}
