<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use greeny\MailLibrary\Drivers\ImapDriver;
use greeny\MailLibrary\Mail;
use greeny\MailLibrary\Connection;
use App\Models\message;
use App\Models\attachment;
use App\Models\address;
use App\Models\application;
use App\Models\history;
use App\Models\User;
use App\Models\notify;
use App\Models\mailbox;
use App\Models\status_message;
use App\Mail\user\appnotify;
use \ConvertApi\ConvertApi;
use Illuminate\Support\Facades\Mail as Mail_send;
use App\Events\MessageSent;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\status_application;
use App\Models\ApplicationStatus;
use App\Models\estimate;
use Carbon\Carbon;
use Tochka\Calendar\WorkCalendar;
use \ZanySoft\Zip\Facades\Zip;
use Illuminate\Support\Facades\Storage;




class ImapController extends Controller
{
    public function vsk_parse($subject, $email, $from, $to, $attachments, $our_company, $uid, $id_status)
    {

        $text = $email->getHTMLBody();
        $app_number = ' ';
        $model =  ' ';
        $vin =  ' ';
        $gnz = ' ';
        $app_number = preg_replace('/[^0-9]/', '', $subject);
        if ($app_number[0] == 9)

            $app_number = mb_substr($app_number, 0, 7);
        else
            $app_number = mb_substr($app_number, 0, 8);

        $start_pos_app_number = strripos($subject, ':');
        if (
            $start_pos_app_number === false
        ) {
            $start_pos_app_number;
        } else {
            $start_pos_app_number++;
        }
        $finish_pos_app_number = mb_strripos($subject, '_');

        // dd($finish_pos_app_number);
        if (!$finish_pos_app_number) {
            $start_pos_app_number = strripos($subject, 'Направление');
            echo $start_pos_app_number;

            if (
                $start_pos_app_number === false
            ) {
                $start_pos_app_number;
            } else {
                $start_pos_app_number += 14;
            }
            if (!$start_pos_app_number) {
            } else {
                echo $app_number . '<br>';
                $start_pos_vin = mb_strripos($subject, ' ');
                $vin = mb_substr($subject, $start_pos_vin + 1, iconv_strlen($subject) - $start_pos_vin);
                $subject = mb_substr($subject, 0, $start_pos_vin);
                $start_pos_gnz = mb_strripos($subject, ' ');

                $model = mb_substr($subject, $start_pos_app_number + 8, $start_pos_gnz - $start_pos_app_number - 8);
                echo $model . '<br>';
                $gnz = mb_substr($subject, $start_pos_gnz + 1, iconv_strlen($subject) - $start_pos_gnz);
                echo $gnz . '<br>';
            }
            $model = $model ? $model : ' ';
            $vin = $vin ? $vin : ' ';
            $gnz = $gnz ? $gnz : ' ';
            $start_pos = mb_strpos($text, '29.%20%');
            $final_post = mb_strpos($text, '%231');
            $number = mb_substr($text, $start_pos + 9, $final_post - $start_pos - 9);
            echo $number . '<br>';
            $city = ' ';
            if ($our_company == 2)
                $city = 3;
            //dd($start_pos_app_number, $subject, $start_pos_gnz, $app_number, $model, $vin, $gnz);
            $applications = application::updateorcreate(
                ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 1, 'our_company' => $our_company],
                [
                    'numberInsuranceCompanyApplication' => $app_number,
                    'id_insuranceCompany' => 1,
                    'CarModel' => $model,
                    'gnz' => $gnz,
                    'id_status' => $id_status,
                    'vin' => $vin,
                    'ub_type' => false,
                    'ic_answer_date' => NULL,
                    'ub_number' => (int)$number,
                    'id_city' => $city,
                ]
            );
            try {
                $estimate = estimate::findorfail('id_application', '=', $applications->id);
            } catch (\Exception $e) {
                $estimate = estimate::firstorcreate(
                    ['id_application' => $applications->id],
                    [
                        'id_application' => $applications->id,
                        'price_sp_full' => 0,
                        'price_sp_half' => 0,
                        'price_repair' => 0,
                        'price_paint' => 0,
                        'price_zip' => 0,
                        'price_work' => 0,
                        'price_repair_full' => 0,
                        'price_repair_half' => 0
                    ]
                );
            }
            // echo $text;
            $pattern = '~[a-z]+://\S+~';

            $str = $text;
            $message = new Message();
            $message->subject = $email->getSubject();
            $message->uid = $uid;
            $message->body_html = $email->getHTMLBody();
            $message->body_text = $email->getTextBody();
            $message->header = $email->getHeaders();
            $message->message_date = $email->getdate();
            $message->application_id = $applications->id;
            $message->is_parsed = true;
            $message->mailbox_id = $our_company;
            $applications->ic_answer_date = NULL;
            $message->save();
            if ($num_found = preg_match_all($pattern, $str, $out)) {
                echo "FOUND " . $num_found . " LINKS:\n";
                // dd($out[0][0]);
            }
            $fin_pos = 0;
            if (isset($out[0][0])) {
                $fin_pos = mb_strpos($out[0][0], '>');
                if ($fin_pos > 0)
                    $link = mb_substr($out[0][0], 0, $fin_pos);
                else
                    $link = $out[0][0];
                print_r($link);

                $dir = 'storage/files/' . $applications->id;
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                if ($link) {
                    file_put_contents($dir . "/Tmpfile.zip", fopen($link, 'r'));
                }
                $zip = Zip::open($dir . '/Tmpfile.zip');
                $zip->extract($dir . '/tmp');
                $zip->close();
                Storage::delete("public/files/'.$applications->id.'/Tmpfile.zip");
                $id = $applications->id;
                $att = scandir('storage/files/' . $id . '/tmp');
                foreach ($att as $file) {
                    if ($file != '.' && $file != '..') {
                        $filename = pathinfo($file, PATHINFO_FILENAME);
                        $attach = new attachment();
                        $attach->message_id = $message->id;
                        $attach->id_application = $applications->id;
                        $attach->location = $file;
                        $attach->mime_type = pathinfo($file, PATHINFO_EXTENSION);
                        $attach->file_size = filesize('storage/files/' . $id . "/tmp/" . $file);
                        $attach->file_name = $filename;
                        $attach->save();
                        copy('storage/files/' . $id . "/tmp/" . $file, 'storage/files/' . $id . "/" . $file);
                        unlink('storage/files/' . $id . "/tmp/" . $file);
                    }
                }
                rmdir('storage/files/' . $id . '/tmp');
            }
            // dd($applications);

            $id = $applications->id;
            if ($applications->id_status == 1) {
                $history = new History;
                $history->id_application = $applications->id;
                $history->id_status = 1;
                $history->id_user = 2;
                $history->save();
            }

            $text = strip_tags($email->getHTMLBody());
            // dd($text);
            $dir = 'storage/files/' . $applications->id;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $att = "\n <b><u>Вложения</u></b>\n\n";
            $keyboard = [];
            $i = 0;

            foreach ($attachments as $attachment) {
                $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
                $attachment->save($path = $dir . '/', $filename);
                $attach = new attachment();
                $attach->message_id = $message->id;
                $attach->id_application = $applications->id;
                $attach->location = $filename;
                $attach->mime_type = $attachment->getMimeType();
                $attach->file_size = $attachment->getSize();
                $attach->file_name = $attachment->getFilename();
                $attach->save();
                if (mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                    $history = new history;
                    $history->id_user = 1;
                    $history->id_application = $applications->id;

                    $applications->id_status = 12;

                    $today = WorkCalendar::now();
                    $today->addWorkDays(30);
                    $applications->end_date = $today;

                    $applications->napr_date = carbon::now();
                    $history->id_status = 12;
                    $applications->save();
                    $history->save();

                    try {

                        $user = user::findorfail($applications->id_MRK);
                        $notify = new notify;
                        $notify->id_user = $user->id;
                        $notify->id_application = $id;
                        $notify->id_text = 3;
                        $notify->link = "/application/";

                        $notify_id = $notify->id;
                        $status_application = new status_application;
                        $status_application->id_user = $user->id;
                        $status_application->id_application = $id;
                        $status_application->is_readed = false;
                        $status_application->save();
                        if ($user->telegram_notify) {
                            $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                            $status = ApplicationStatus::findOrFail(
                                $applications->id_status
                            )->StatusName;
                            $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                                ->keyboard(
                                    Keyboard::make()
                                        ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                                )
                                ->send();
                            $notify->id_message = json_decode($ch)->result->message_id;
                        }
                        $notify->save();


                        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                            ->select('notifies.*', 'text_notifies.text')->first();
                        broadcast(new MessageSent($notification));
                    } catch (\Exception $e) {
                        // dd($e);
                    }
                }

                if (
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
                )
                    $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
                else
                    $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

                $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
                $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
            }
            // dd($keyboard);
            //echo $to;
            foreach ($to as $mail) {
                // dd($mail);
                $address = new address;
                $address->message_id = $message->id;
                $address->email = $mail->mail;
                $address->type = 'to';
                $address->save();
            }
            //dd($from, $to, $address);
            foreach ($from as $mail) {
                $address = new address;
                $address->message_id = $message->id;
                $address->email = $mail->mail;
                $address->type = 'from';
                $address->save();
            }
            $user = User::find(2);
            // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
            $notify_id = 0;
            if ($applications->id_status != 27) {

                $users = user::join('user_roles', function ($join) {
                    $join->on('user_roles.id_user', '=', 'users.id')
                        ->where('user_roles.id_group', '=', 2);
                })
                    ->join('role_companies', function ($join) use ($our_company) {
                        $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                    })
                    ->select('users.*')
                    ->get();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 1;
                    $notify->link = "/mail/";

                    $notify_id = $notify->id;
                    $status_message = new status_message;
                    $status_message->id_user = $user->id;
                    $status_message->id_application = $id;
                    $status_message->is_readed = false;
                    $status_message->save();
                    $url = "https://mandarin-portal.ru/mail/" . $id;
                    if ($user->telegram_notify) {

                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        echo $att;
                        $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons($keyboard)
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }
                    $notify->save();
                }

                $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                    ->select('notifies.*', 'text_notifies.text')->first();
                broadcast(new MessageSent($notification));
            }
        } else {
            // echo $app_number . '<br>';
            $start_pos_gnz = mb_strripos($subject, ' ');

            $model = mb_substr($subject, $finish_pos_app_number + 8, $start_pos_gnz - $finish_pos_app_number - 8);
            // echo $model . '<br>';
            $gnz = mb_substr($subject, $start_pos_gnz, iconv_strlen($subject) - $start_pos_gnz);
            echo $gnz . '<br>';

            $app_number = $app_number ? $app_number : ' ';
            $model = $model ? $model : ' ';
            $gnz = $gnz ? $gnz : ' ';
            $applications = application::firstorcreate(
                ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 1, 'our_company' => $our_company],
                [
                    'numberInsuranceCompanyApplication' => $app_number,
                    'id_insuranceCompany' => 1,
                    'CarModel' => $model,
                    'gnz' => $gnz,
                    'id_status' => $id_status,
                    'ic_answer_date' => null,
                ]
            );
            try {
                $estimate = estimate::findorfail('id_application', '=', $applications->id);
            } catch (\Exception $e) {
                $estimate = estimate::firstorcreate(
                    ['id_application' => $applications->id],
                    [
                        'id_application' => $applications->id,
                        'price_sp_full' => 0,
                        'price_sp_half' => 0,
                        'price_repair' => 0,
                        'price_paint' => 0,
                        'price_zip' => 0,
                        'price_work' => 0,
                        'price_repair_full' => 0,
                        'price_repair_half' => 0
                    ]
                );
            }
            // dd($applications);
            $message = new Message();
            $message->subject = $email->getSubject();
            $message->body_html = $email->getHTMLBody();
            $message->body_text = $email->getTextBody();
            $message->header = $email->getHeaders();
            $message->message_date = $email->getdate();
            $message->application_id = $applications->id;
            $id = $applications->id;
            if ($applications->id_status == 1) {
                $history = new History;
                $history->id_application = $applications->id;
                $history->id_status = 1;
                $history->id_user = 2;
                $history->save();
            }
            $message->is_parsed = true;
            $message->save();
            $text = strip_tags($email->getHTMLBody());
            // dd($text);
            $dir = 'storage/files/' . $applications->id;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $att = "\n <b><u>Вложения</u></b>\n\n";
            $keyboard = [];
            $i = 0;

            foreach ($attachments as $attachment) {
                $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
                $attachment->save($path = $dir . '/', $filename);
                $attach = new attachment();
                $attach->message_id = $message->id;
                $attach->id_application = $applications->id;
                $attach->location = $filename;
                $attach->mime_type = $attachment->getMimeType();
                $attach->file_size = $attachment->getSize();
                $attach->file_name = $attachment->getFilename();
                $attach->save();
                if (mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                    $history = new history;
                    $history->id_user = 1;
                    $history->id_application = $applications->id;

                    $applications->id_status = 12;

                    $today = WorkCalendar::now();
                    $today->addWorkDays(30);
                    $applications->end_date = $today;

                    $applications->napr_date = carbon::now();
                    $history->id_status = 12;
                    $applications->save();
                    $history->save();

                    try {

                        $user = user::findorfail($applications->id_MRK);
                        $notify = new notify;
                        $notify->id_user = $user->id;
                        $notify->id_application = $id;
                        $notify->id_text = 3;
                        $notify->link = "/application/";


                        $notify_id = $notify->id;
                        $status_application = new status_application;
                        $status_application->id_user = $user->id;
                        $status_application->id_application = $id;
                        $status_application->is_readed = false;
                        $status_application->save();
                        if ($user->telegram_notify) {
                            $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                            $status = ApplicationStatus::findOrFail(
                                $applications->id_status
                            )->StatusName;
                            $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                                ->keyboard(
                                    Keyboard::make()
                                        ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                                )
                                ->send();
                            $notify->id_message = json_decode($ch)->result->message_id;
                        }
                        $notify->save();

                        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                            ->select('notifies.*', 'text_notifies.text')->first();
                        broadcast(new MessageSent($notification));
                    } catch (\Exception $e) {
                        // dd($e);
                    }
                }

                if (
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
                )
                    $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
                else
                    $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

                $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
                $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
            }
            // dd($keyboard);
            //echo $to;
            foreach ($to as $mail) {
                // dd($mail);
                $address = new address;
                $address->message_id = $message->id;
                $address->email = $mail->mail;
                $address->type = 'to';
                $address->save();
            }
            //dd($from, $to, $address);
            foreach ($from as $mail) {
                $address = new address;
                $address->message_id = $message->id;
                $address->email = $mail->mail;
                $address->type = 'from';
                $address->save();
            }
            $user = User::find(2);
            // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
            $notify_id = 0;
            if ($applications->id_status != 27) {
                $users = user::join('user_roles', function ($join) {
                    $join->on('user_roles.id_user', '=', 'users.id')
                        ->where('user_roles.id_group', '=', 2);
                })
                    ->join('role_companies', function ($join) use ($our_company) {
                        $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                    })

                    ->select('users.*')
                    ->get();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 1;
                    $notify->link = "/mail/";
                    $notify_id = $notify->id;
                    $status_message = new status_message;
                    $status_message->id_user = $user->id;
                    $status_message->id_application = $id;
                    $status_message->is_readed = false;
                    $status_message->save();
                    $url = "https://mandarin-portal.ru/mail/" . $id;
                    if ($user->telegram_notify) {

                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        // echo $att;
                        $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons($keyboard)
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }

                $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                    ->select('notifies.*', 'text_notifies.text')->first();
                broadcast(new MessageSent($notification));
            }
        }
    }
    public function ingos_parse($subject, $email, $from, $to, $attachments, $our_company, $uid, $id_status)
    {
        echo $subject . '<br>';

        $array_subject = explode(' ', $subject);
        $gnz = '';
        $app_number = '';

        // $trim = mb_strripos($subject, ' ');
        // $subject = mb_substr($subject, 0, $trim);
        // $start_pos_gnz = mb_strripos($subject, ' ');
        // $gnz = mb_substr($subject, $start_pos_gnz + 1, iconv_strlen($subject) - $start_pos_gnz);
        // echo $gnz . '<br>';
        // $subject = mb_substr($subject, 0, $start_pos_gnz);
        $subject = str_replace('Fwd:', '', $subject);
        $subject = str_replace('Re:', '', $subject);

        $subject = str_replace('FWD:', '', $subject);

        $subject = str_replace('FW:', '', $subject);
        $subject = str_replace('RE:', '', $subject);

        $subject = str_replace('fwd:', '', $subject);
        $subject = str_replace('re:', '', $subject);
        $start_pos = mb_strpos($subject, '707-');
        $subject = mb_substr($subject, $start_pos);
        $subject = preg_replace("/\t/", ' ', $subject);
        $subject = trim($subject);
        $finish_pos = mb_strpos($subject, ' ');
        // dd($start_pos);
        $app_number = mb_substr($subject, 0, $finish_pos);
        $app_number = trim($app_number);
        $find707 = mb_strpos($app_number, '-');
        if (!$find707) {
            $app_number = "707-75-" . $app_number;
        }
        $find24 = mb_strpos($app_number, '/');
        if (!$find24) {
            $app_number =  $app_number . "/25";
        }
        $app_number = substr($app_number, 0, 17);
        // dd($app_number);
        // echo $app_number . '<br>';
        // $start_pos = mb_strpos($subject, 'ремонт');
        // $model = mb_substr($subject, $start_pos + 7, iconv_strlen($subject) - $start_pos);
        // echo $model . '<br>';

        $applications = application::firstorcreate(
            ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 3, 'our_company' => $our_company],
            [
                'numberInsuranceCompanyApplication' => $app_number,
                'id_insuranceCompany' => 3,
                'CarModel' => " ",
                'gnz' => $gnz,
                'id_status' => $id_status,
                'vin' => " ",
                'ub_type' => false,
                'our_company' => $our_company,
                'ic_answer_date' => null,
            ]
        );
        try {
            $estimate = estimate::findorfail('id_application', '=', $applications->id);
        } catch (\Exception $e) {
            $estimate = estimate::firstorcreate(
                ['id_application' => $applications->id],
                [
                    'id_application' => $applications->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        }
        // dd($applications);
        $message = new Message();
        $message->subject = $email->getSubject();
        $message->uid = $uid;
        $message->body_html = $email->getHTMLBody();
        $message->body_text = $email->getTextBody();
        $message->header = $email->getHeaders();
        $message->message_date = $email->getdate();
        $message->application_id = $applications->id;
        $id = $applications->id;
        if ($applications->id_status == 1) {
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
        }
        $message->is_parsed = true;

        $message->mailbox_id = $our_company;
        $message->save();
        $dir = 'storage/files/' . $applications->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $att = "\n <b><u>Вложения</u></b>\n\n";
        $keyboard = [];
        $i = 0;
        foreach ($attachments as $attachment) {
            $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
            $attachment->save($path = $dir . '/', $filename);
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = $attachment->getMimeType();
            $attach->file_size = $attachment->getSize();
            $attach->file_name = $attachment->getFilename();
            $attach->save();
            if (mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                $history = new history;
                $history->id_user = 1;
                $history->id_application = $applications->id;

                $applications->id_status = 12;

                $today = WorkCalendar::now();
                $today->addWorkDays(30);
                $applications->end_date = $today;

                $applications->napr_date = carbon::now();
                $history->id_status = 12;
                $applications->save();
                $history->save();

                try {

                    $user = user::findorfail($applications->id_MRK);
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $applications->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();


                    $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                        ->select('notifies.*', 'text_notifies.text')->first();
                    broadcast(new MessageSent($notification));
                } catch (\Exception $e) {
                    // dd($e);
                }
            }

            if (
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
            )
                $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
            else
                $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

            $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
            $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        }
        //echo $to;
        foreach ($to as $mail) {
            // dd($mail);
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'to';
            $address->save();
        }
        //dd($from, $to, $address);
        foreach ($from as $mail) {
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'from';
            $address->save();
        }
        $user = User::find(2);
        // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        if ($applications->id_status != 27) {
            $users = user::join('user_roles', function ($join) {
                $join->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 2);
            })
                ->join('role_companies', function ($join) use ($our_company) {
                    $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                })

                ->select('users.*')
                ->get();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify_id = $notify->id;
                $status_message = new status_message;
                $status_message->id_user = $user->id;
                $status_message->id_application = $id;
                $status_message->is_readed = false;
                $status_message->save();
                $url = "https://mandarin-portal.ru/mail/" . $id;
                if ($user->telegram_notify) {

                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    // echo $att;
                    $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons($keyboard)
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }
                $notify->save();
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
        }
    }

    public function sogaz_parse($subject, $email, $from, $to, $attachments, $our_company, $uid)
    {
        $gnz = ' ';
        $app_number = ' ';
        $ub_type = false;
        $client = ' ';
        $vin = ' ';
        $ub_number = ' ';
        $CarModel = ' ';
        $id_status = 1;
        if (mb_strpos($subject, "/ID/") !== false) {
            $start = mb_strpos($subject, "ID/");
            $array_subject = explode('/', mb_substr($subject, $start));
            $start = mb_strpos($email->getHTMLBody(), "Заявление");
            $body = explode('<br>', mb_substr($email->getHTMLBody(), $start + 11));
            $app_number = trim($body[0]);
            $start = mb_strpos($email->getHTMLBody(), "VIN");
            $body = explode('<br>', mb_substr($email->getHTMLBody(), $start + 5));
            $vin = trim($body[0]);
            if (mb_strpos($email->getHTMLBody(), "КАСКО"))
                $ub_type = true;
            $client = $array_subject[3];
            $ub_number = $array_subject[1];
            $CarModel = $array_subject[4];
            $gnz = $array_subject[5];
            $id_status = 1;

            // dd($body, $array_subject);
        } else {
            if ($start = mb_strpos($subject, 'SGZA')) {
                $app_number = mb_substr($subject, $start, 23);
                $ub_type = true;
            } else if ($start = mb_strpos($subject, 'XXX')) {
                $app_number = mb_substr($subject, $start, 20);
            } else if ($start = mb_strpos($subject, 'TTT')) {
                $app_number = mb_substr($subject, $start, 20);
            } else if ($start = mb_strpos($subject, 'xxx')) {
                $app_number = mb_substr($subject, $start, 20);
            } else if ($start = mb_strpos($subject, 'ttt')) {
                $app_number = mb_substr($subject, $start, 20);
            }
            // dd($subject);
        }
        echo $subject . '<br>';
        $applications = application::firstorcreate(
            ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 5, 'our_company' => 6],
            [
                'numberInsuranceCompanyApplication' => $app_number,
                'id_insuranceCompany' => 5,
                'CarModel' => $CarModel,
                'gnz' => $gnz,
                'vin' => $vin,
                'ub_type' => $ub_type,
                'our_company' => 6,
                'ic_answer_date' => null,
                'ClientName' => $client,
                'ub_number' => $ub_number,
                'id_status' => $id_status,


            ]
        );
        // dd($applications);
        try {
            $estimate = estimate::firstorcreate(
                ['id_application' => $applications->id],
                [
                    'id_application' => $applications->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        } catch (\Exception $e) {
            $estimate = estimate::firstorcreate(
                ['id_application' => $applications->id],
                [
                    'id_application' => $applications->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        }
        // dd($applications);
        $message = new Message();
        $message->subject = $email->getSubject();
        $message->uid = $uid;
        $message->body_html = $email->getHTMLBody();
        $message->body_text = $email->getTextBody();
        $message->header = $email->getHeaders();
        $message->message_date = $email->getdate();
        $message->application_id = $applications->id;
        $id = $applications->id;
        if ($applications->id_status == 1) {
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
        }
        $message->is_parsed = true;

        $message->mailbox_id = $our_company;
        $message->save();
        $dir = 'storage/files/' . $applications->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $att = "\n <b><u>Вложения</u></b>\n\n";
        $keyboard = [];
        $i = 0;
        foreach ($attachments as $attachment) {
            $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
            $attachment->save($path = $dir . '/', $filename);
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = $attachment->getMimeType();
            $attach->file_size = $attachment->getSize();
            $attach->file_name = $attachment->getFilename();
            $attach->save();
            if (mb_strripos('_' . $filename, 'Napravlenie') || mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                $history = new history;
                $history->id_user = 1;
                $history->id_application = $applications->id;

                $applications->id_status = 12;

                $today = WorkCalendar::now();
                $today->addWorkDays(30);
                $applications->end_date = $today;

                $applications->napr_date = carbon::now();
                $history->id_status = 12;
                $applications->save();
                $history->save();

                try {

                    $user = user::findorfail($applications->id_MRK);
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $applications->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();


                    $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                        ->select('notifies.*', 'text_notifies.text')->first();
                    broadcast(new MessageSent($notification));
                } catch (\Exception $e) {
                    // dd($e);
                }
            }

            if (
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
            )
                $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
            else
                $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

            $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
            $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        }
        //echo $to;
        foreach ($to as $mail) {
            // dd($mail);
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'to';
            $address->save();
        }
        //dd($from, $to, $address);
        foreach ($from as $mail) {
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'from';
            $address->save();
        }
        $user = User::find(2);
        // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        if ($applications->id_status != 27) {
            $users = user::join('user_roles', function ($join) {
                $join->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 2);
            })
                ->join('role_companies', function ($join) use ($our_company) {
                    $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                })

                ->select('users.*')
                ->get();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify_id = $notify->id;
                $status_message = new status_message;
                $status_message->id_user = $user->id;
                $status_message->id_application = $id;
                $status_message->is_readed = false;
                $status_message->save();
                $url = "https://mandarin-portal.ru/mail/" . $id;
                if ($user->telegram_notify) {

                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    // echo $att;
                    $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons($keyboard)
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }
                $notify->save();
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
        }
    }
    public function ugor_parse($subject, $email, $from, $to, $attachments, $our_company, $uid)
    {
        echo $subject . '<br>';

        $array_subject = explode(' ', $subject);
        $gnz = '';
        $app_number = '';

        // $trim = mb_strripos($subject, ' ');
        // $subject = mb_substr($subject, 0, $trim);
        // $start_pos_gnz = mb_strripos($subject, ' ');
        // $gnz = mb_substr($subject, $start_pos_gnz + 1, iconv_strlen($subject) - $start_pos_gnz);
        // echo $gnz . '<br>';
        // $subject = mb_substr($subject, 0, $start_pos_gnz);
        $subject = str_replace('Fwd:', '', $subject);
        $subject = str_replace('Re:', '', $subject);

        $subject = str_replace('FWD:', '', $subject);

        $subject = str_replace('FW:', '', $subject);
        $subject = str_replace('RE:', '', $subject);

        $subject = str_replace('fwd:', '', $subject);
        $subject = str_replace('re:', '', $subject);
        $start_pos = mb_strpos($subject, '707-');
        $subject = mb_substr($subject, $start_pos);
        $subject = preg_replace("/\t/", ' ', $subject);
        $subject = trim($subject);

        // dd($start_pos);
        // $app_number = mb_substr($subject, 0, $finish_pos);
        // $app_number = trim($app_number);
        // $find707 = mb_strpos($app_number, '-');
        // if (!$find707) {
        //     $app_number = "707-75-" . $app_number;
        // }
        // $find24 = mb_strpos($app_number, '/');
        // if (!$find24) {
        //     $app_number =  $app_number . "/25";
        // }
        $start_pos = mb_strpos($subject, '№ ');
        if ($start_pos != 0)
            $start_pos += 2;
        $app_number = mb_substr($subject, $start_pos, 16);
        // dd($app_number);
        // echo $app_number . '<br>';
        // $start_pos = mb_strpos($subject, 'ремонт');
        // $model = mb_substr($subject, $start_pos + 7, iconv_strlen($subject) - $start_pos);
        // echo $model . '<br>';

        $applications = application::firstorcreate(
            ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 7, 'our_company' => $our_company],
            [
                'numberInsuranceCompanyApplication' => $app_number,
                'id_insuranceCompany' => 7,
                'CarModel' => " ",
                'gnz' => " ",
                'id_status' => 1,
                'vin' => " ",
                'ub_type' => false,
                'our_company' => $our_company,
                'ic_answer_date' => null,
            ]
        );
        try {
            $estimate = estimate::findorfail('id_application', '=', $applications->id);
        } catch (\Exception $e) {
            $estimate = estimate::firstorcreate(
                ['id_application' => $applications->id],
                [
                    'id_application' => $applications->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        }
        $pattern = '~[a-z]+://\S+~';

        $str = $email->getHTMLBody();
        $link = '';

        if ($num_found = preg_match_all($pattern, $str, $out)) {
            echo "FOUND " . $num_found . " LINKS:\n";
        }
        if (isset($out[0][0])) {
            $fin_pos = mb_strpos($out[0][0], '"');
            if ($fin_pos)
                $link = mb_substr($out[0][0], 0, $fin_pos);
            else
                $link = $out[0][0];
            print_r($link);
            echo $out[0][0] . '<br>';
        }

        $dir = 'storage/files/' . $applications->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (isset($link)) {
            $base_url = 'https://cloud-api.yandex.net/v1/disk/public/resources/download?';
            $final_url = $base_url . 'public_key=' . $link;
            echo $final_url . '<br>';
            /**
             * Получение данных ответа
             */
            $ch = curl_init($final_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            $http_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            /**
             * Проверка HTTP кода
             */
            if ($http_code === 200) {
                $json = json_decode(file_get_contents($final_url));
                $linkFile = $json->href;

                /**
                 * Путь куда сохранять файл
                 */

                $filename = 'foto';
                $filename .= "_" . date('d-m-y_hms') . ".zip";
                $path = $dir . '/' . $filename;
                file_put_contents($path, file_get_contents($linkFile));
            } else {
                // throw new RuntimeException('Ошибка 404. Страница файла указана неверно.');
            }
        }
        // dd($email->getTextBody());

        $message = new Message();
        $message->subject = $email->getSubject();
        $message->uid = $uid;
        $message->body_html = $email->getHTMLBody();
        $message->body_text = $email->getTextBody();
        $message->header = $email->getHeaders();
        $message->message_date = $email->getdate();
        $message->application_id = $applications->id;
        $id = $applications->id;
        if ($applications->id_status == 1) {
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
        }
        $message->is_parsed = true;

        $message->mailbox_id = $our_company;
        $message->save();
        if (isset($filename)) {
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = 'zip';
            $attach->file_size = 0;
            $attach->file_name = 'foto';
            $attach->save();
        }
        $att = "\n <b><u>Вложения</u></b>\n\n";
        $keyboard = [];
        $i = 0;
        foreach ($attachments as $attachment) {
            $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
            $attachment->save($path = $dir . '/', $filename);
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = $attachment->getMimeType();
            $attach->file_size = $attachment->getSize();
            $attach->file_name = $attachment->getFilename();
            $attach->save();
            if (mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                $history = new history;
                $history->id_user = 1;
                $history->id_application = $applications->id;

                $applications->id_status = 12;

                $today = WorkCalendar::now();
                $today->addWorkDays(30);
                $applications->end_date = $today;

                $applications->napr_date = carbon::now();
                $history->id_status = 12;
                $applications->save();
                $history->save();

                try {

                    $user = user::findorfail($applications->id_MRK);
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $applications->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();


                    $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                        ->select('notifies.*', 'text_notifies.text')->first();
                    broadcast(new MessageSent($notification));
                } catch (\Exception $e) {
                    // dd($e);
                }
            }

            if (
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
            )
                $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
            else
                $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

            $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
            $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        }
        //echo $to;
        foreach ($to as $mail) {
            // dd($mail);
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'to';
            $address->save();
        }
        //dd($from, $to, $address);
        foreach ($from as $mail) {
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'from';
            $address->save();
        }
        $user = User::find(2);
        // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        if ($applications->id_status != 27) {
            $users = user::join('user_roles', function ($join) {
                $join->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 2);
            })
                ->join('role_companies', function ($join) use ($our_company) {
                    $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                })

                ->select('users.*')
                ->get();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify_id = $notify->id;
                $status_message = new status_message;
                $status_message->id_user = $user->id;
                $status_message->id_application = $id;
                $status_message->is_readed = false;
                $status_message->save();
                $url = "https://mandarin-portal.ru/mail/" . $id;
                if ($user->telegram_notify) {

                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    // echo $att;
                    $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons($keyboard)
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }
                $notify->save();
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
        }
    }
    public function reso_parse($subject, $email, $from, $to, $attachments, $our_company, $uid)
    {
        echo $subject . '<br>';


        $applications = application::firstorcreate(
            ['numberInsuranceCompanyApplication' => date('dmY_hms'), 'id_insuranceCompany' => 4, 'our_company' => 6],
            [
                'numberInsuranceCompanyApplication' => date('dmY_hms'),
                'id_insuranceCompany' => 4,
                'CarModel' => " ",
                'gnz' => '',
                'id_status' => 1,
                'vin' => " ",
                'ub_type' => false,
                'our_company' => 6,
                'ic_answer_date' => null,
            ]
        );
        try {
            $estimate = estimate::findorfail('id_application', '=', $applications->id);
        } catch (\Exception $e) {
            $estimate = estimate::firstorcreate(
                ['id_application' => $applications->id],
                [
                    'id_application' => $applications->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        }
        // dd($applications);
        $message = new Message();
        $message->subject = $email->getSubject();
        $message->uid = $uid;
        $message->body_html = $email->getHTMLBody();
        $message->body_text = $email->getTextBody();
        $message->header = $email->getHeaders();
        $message->message_date = $email->getdate();
        $message->application_id = $applications->id;
        $id = $applications->id;
        if ($applications->id_status == 1) {
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
        }
        $message->is_parsed = true;

        $message->mailbox_id = $our_company;
        $message->save();
        $dir = 'storage/files/' . $applications->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $att = "\n <b><u>Вложения</u></b>\n\n";
        $keyboard = [];
        $i = 0;
        foreach ($attachments as $attachment) {
            $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
            $attachment->save($path = $dir . '/', $filename);
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = $attachment->getMimeType();
            $attach->file_size = $attachment->getSize();
            $attach->file_name = $attachment->getFilename();
            $attach->save();


            if (
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
            )
                $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
            else
                $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

            $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
            $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        }
        //echo $to;
        foreach ($to as $mail) {
            // dd($mail);
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'to';
            $address->save();
        }
        //dd($from, $to, $address);
        foreach ($from as $mail) {
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'from';
            $address->save();
        }
        $user = User::find(2);
        // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        // if ($applications->id_status != 27) {
        //     $users = user::join('user_roles', function ($join) {
        //     $join->on('user_roles.id_user', '=', 'users.id')
        //         ->where('user_roles.id_group', '=', 2);
        // })
        //     ->join('role_companies', function ($join) use ($our_company) {
        //         $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
        //     })
        //     ->get();
        //     foreach ($users as $user) {
        //         $notify = new notify;
        //         $notify->id_user = $user->id;
        //         $notify->id_application = $id;
        //         $notify->id_text = 1;
        //         $notify->link = "/mail/";
        //         $notify_id = $notify->id;
        //         $status_message = new status_message;
        //         $status_message->id_user = $user->id;
        //         $status_message->id_application = $id;
        //         $status_message->is_readed = false;
        //         $status_message->save();
        //         $url = "https://mandarin-portal.ru/mail/" . $id;
        //         if ($user->telegram_notify) {

        //             $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
        //             // echo $att;
        //             $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
        //                 ->keyboard(
        //                     Keyboard::make()
        //                         ->buttons($keyboard)
        //                 )
        //                 ->send();
        //             $notify->id_message = json_decode($ch)->result->message_id;
        //         }
        //         $notify->save();
        //     }

        //     $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
        //         ->select('notifies.*', 'text_notifies.text')->first();
        //     broadcast(new MessageSent($notification));
        // }
    }

    public function pzn_parse($subject, $email, $from, $to, $attachments, $our_company, $uid, $app_id)
    {
        echo $subject . '<br>';

        $array_subject = explode(' ', $subject);
        $gnz = '';
        $app_number = '';

        // $trim = mb_strripos($subject, ' ');
        // $subject = mb_substr($subject, 0, $trim);
        // $start_pos_gnz = mb_strripos($subject, ' ');
        // $gnz = mb_substr($subject, $start_pos_gnz + 1, iconv_strlen($subject) - $start_pos_gnz);
        // echo $gnz . '<br>';
        // $subject = mb_substr($subject, 0, $start_pos_gnz);

        $applications = application::firstorcreate(
            ['id' => $app_id],
            [

                'ic_answer_date' => null,
            ]
        );

        // dd($applications);
        $message = new Message();
        $message->subject = $email->getSubject();
        $message->uid = $uid;
        $message->body_html = $email->getHTMLBody();
        $message->body_text = $email->getTextBody();
        $message->header = $email->getHeaders();
        $message->message_date = $email->getdate();
        $message->application_id = $app_id;
        $id = $app_id;

        $message->is_parsed = true;

        $message->mailbox_id = $our_company;
        $message->save();
        $dir = 'storage/files/' . $app_id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $att = "\n <b><u>Вложения</u></b>\n\n";
        $keyboard = [];
        $i = 0;
        foreach ($attachments as $attachment) {
            $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
            $attachment->save($path = $dir . '/', $filename);
            $attach = new attachment();
            $attach->message_id = $message->id;
            $attach->id_application = $applications->id;
            $attach->location = $filename;
            $attach->mime_type = $attachment->getMimeType();
            $attach->file_size = $attachment->getSize();
            $attach->file_name = $attachment->getFilename();
            $attach->save();
            if (mb_strripos('_' . $filename, 'Направление') || mb_strripos('_' . $filename, 'направление')) {
                $history = new history;
                $history->id_user = 1;
                $history->id_application = $applications->id;

                $applications->id_status = 12;

                $today = WorkCalendar::now();
                $today->addWorkDays(30);
                $applications->end_date = $today;

                $applications->napr_date = carbon::now();
                $history->id_status = 12;
                $applications->save();
                $history->save();

                try {

                    $user = user::findorfail($applications->id_MRK);
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $applications->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();


                    $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                        ->select('notifies.*', 'text_notifies.text')->first();
                    broadcast(new MessageSent($notification));
                } catch (\Exception $e) {
                    // dd($e);
                }
            }

            if (
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
            )
                $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name;
            else
                $path = "https://mandarin-portal.ru/" . $dir . '/' . $filename;

            $att .= "<a href='" . $path . "'>" . pathinfo($attachment->getFilename(), PATHINFO_FILENAME) . "</a>\n ";
            $name = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
            $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        }
        //echo $to;
        foreach ($to as $mail) {
            // dd($mail);
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'to';
            $address->save();
        }
        //dd($from, $to, $address);
        foreach ($from as $mail) {
            $address = new address;
            $address->message_id = $message->id;
            $address->email = $mail->mail;
            $address->type = 'from';
            $address->save();
        }
        $user = User::find(2);
        // Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        if ($applications->id_status != 27) {
            $users = user::join('user_roles', function ($join) {
                $join->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 2);
            })
                ->join('role_companies', function ($join) use ($our_company) {
                    $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $our_company);
                })

                ->select('users.*')
                ->get();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify_id = $notify->id;
                $status_message = new status_message;
                $status_message->id_user = $user->id;
                $status_message->id_application = $id;
                $status_message->is_readed = false;
                $status_message->save();
                $url = "https://mandarin-portal.ru/mail/" . $id;
                if ($user->telegram_notify) {

                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    // echo $att;
                    $ch = $new_chat->html("По заявке {$id} получено новое сообщение от страховой компании \n\n <a href='{$url}'>Открыть в браузере</a>\n")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons($keyboard)
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }
                $notify->save();
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
        }
    }
    public function recieve()
    {

        // $ms = message::join('addresses', 'addresses.message_id', '=', 'messages.id')
        //     ->where('addresses.email', '=', 'vsk_info@vsk.ru')
        //     ->where('addresses.type', '=', 'from')
        //     ->get();
        // foreach ($ms as $m) {
        //     $text = $m->body_html;
        //     $start_pos = mb_strpos($text, '29.%20%');
        //     if ($start_pos) {
        //         $final_post = mb_strpos($text, '%231');
        //         $number = mb_substr($text, $start_pos + 9, $final_post - $start_pos - 9);

        //         $a = application::find((int)$m->application_id);
        //         // dd($a, $m->application_id);
        //         if ($a) {
        //             $a->ub_number = (int) $number;
        //             $a->save();
        //             echo $number . ' ' . $m->application_id . '<br>';
        //         }
        //     }
        // }
        // dd($ms);
        $cm = new ClientManager('config/imap.php');

        /** @var \Webklex\PHPIMAP\Client $client */ {
            $client = $cm->account('default');
            //   dd($client);
            //Connect to the IMAP Server
            $client->connect();

            //Get all Mailboxes
            /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
            $folders = $client->getFolders(true, "INBOX");
            foreach ($folders as $folder) {

                //Get all Messages of the current Mailbox $folder
                /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
                $mailbox = DB::table('mailboxes')->where('mailboxes.id', '=', 1)->first();
                $start_uid = $mailbox->last_message_uid + 1;
                $emailes = $folder->overview($sequence = "$start_uid:*");
                /** @var \Webklex\PHPIMAP\Message $message */
                $last_uid = -1;
                foreach ($emailes as $uid => $email) {

                    echo $uid . "<br />";
                    $last_uid = $uid;
                    $mailbox = mailbox::where('id', '=', 1)->first();
                    $mailbox->last_message_uid = $last_uid;
                    $mailbox->save();
                    $messages = $folder->messages()->where(['uid' => $uid])->get();
                    // dd($messages, $uid);
                    foreach ($messages as $message) {
                        $subject = $message->getSubject();
                        echo $subject . "<br/>";


                        $attachments = $message->getAttachments();

                        $from = $message->getFrom()->toarray();
                        $to = $message->getTo()->toArray();
                        $subject = $message->getSubject();
                        $subject = trim($subject);
                        echo $subject . "<br>";
                        $body_html = $message->getHTMLBody();
                        $our_company = 1;
                        // $subject = "ПЗН LEXUS RX 300 М801ТР29 Каско zn-234!";
                        if (mb_strpos($subject, 'zn-')) {
                            echo "ZN ZN ZN <br>";
                            $app_id_start = mb_strpos($subject, 'zn-');

                            $app_id_end = mb_strpos($subject, '!');
                            $app_id = mb_substr($subject, $app_id_start + 3, $app_id_end - $app_id_start - 3);
                            $this->pzn_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, $app_id);

                            // dd($app_id);
                        } else {
                            $sogaz = 0;
                            foreach ($from as $fr) {
                                if (mb_strpos($fr, 'sogaz') !== false)
                                    $sogaz = 1;
                            }
                            if ($sogaz) {
                                echo "Sogaz <br>";
                                $this->sogaz_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                            }
                            if (mb_strpos($body_html, 'vsk.ru') !== false) {
                                $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 27);
                            } elseif (mb_strpos($body_html, 'ingos') !== false) {
                                echo "Ingos";

                                $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 27);
                            } elseif (mb_strpos($body_html, 'reso') !== false) {
                                $this->reso_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                            } elseif (mb_strpos($body_html, 'Югория') !== false) {
                                $this->ugor_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                            } else {
                                $mess = new Message();
                                $mess->subject = $message->getSubject();
                                $mess->body_html = $message->getHTMLBody();
                                $mess->body_text = $message->getTextBody();
                                $mess->header = $message->getHeaders();
                                $mess->message_date = $message->getdate();
                                $mess->mailbox_id = 1;
                                $mess->save();
                                foreach ($to as $mail) {
                                    // dd($mail);
                                    $address = new address;
                                    $address->message_id = $mess->id;
                                    $address->email = $mail->mail;
                                    $address->type = 'to';
                                    $address->save();
                                }
                                //dd($from, $to, $address);
                                foreach ($from as $mail) {
                                    $address = new address;
                                    $address->message_id = $mess->id;
                                    $address->email = $mail->mail;
                                    $address->type = 'from';
                                    $address->save();
                                }
                                $dir = 'storage/files/temp';
                                if (!is_dir($dir)) {
                                    mkdir($dir, 0777, true);
                                }

                                foreach ($attachments as $attachment) {
                                    $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                                    $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
                                    $attachment->save($path = $dir . '/', $filename);
                                    $attach = new attachment();
                                    $attach->message_id = $mess->id;
                                    $attach->location = $filename;
                                    $attach->mime_type = $attachment->getMimeType();
                                    $attach->file_size = $attachment->getSize();
                                    $attach->file_name = $attachment->getFilename();
                                    $attach->save();
                                }
                            }
                        }
                    }
                }
                if ($last_uid != (-1)) {
                }
            }
        }

        $client1 = $cm->account('kotlas');
        //   dd($client);
        //Connect to the IMAP Server
        $client1->connect();

        //Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $folders = $client1->getFolders(true, "INBOX");
        foreach ($folders as $folder) {

            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $mailbox = DB::table('mailboxes')->where('mailboxes.id', '=', 2)->first();
            $start_uid = $mailbox->last_message_uid + 1;
            $emailes = $folder->overview($sequence = "$start_uid:*");
            /** @var \Webklex\PHPIMAP\Message $message */
            $last_uid = -1;
            foreach ($emailes as $uid => $email) {

                echo "<br />" . $uid . "<br />";
                $last_uid = $uid;
                $mailbox = mailbox::where('id', '=', 2)->first();
                $mailbox->last_message_uid = $last_uid;
                $mailbox->save();
                $messages = $folder->messages()->where(['uid' => $uid])->get();
                // dd($messages, $uid);
                foreach ($messages as $message) {
                    $subject = $message->getSubject();
                    echo $subject . "<br/>";


                    $attachments = $message->getAttachments();

                    $from = $message->getFrom()->toarray();
                    $to = $message->getTo()->toArray();
                    $subject = $message->getSubject();
                    $subject = trim($subject);
                    $body_html = $message->getHTMLBody();
                    $our_company = 2;
                    // dd($message);

                    if (mb_strpos($subject, 'zn-')) {
                        $app_id_start = mb_strpos($subject, 'zn-');

                        $app_id_end = mb_strpos($subject, '!');
                        $app_id = mb_substr($subject, $app_id_start + 3, $app_id_end - $app_id_start - 3);
                        $this->pzn_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, $app_id);

                        // dd($app_id);
                    } else {
                        if (mb_strpos($body_html, 'vsk.ru') !== false) {
                            $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 1);
                        } elseif (mb_strpos($body_html, 'ingos') !== false) {
                            echo "Ingos";

                            $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 1);
                        } elseif (mb_strpos($body_html, 'Югория') !== false) {
                            $this->ugor_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                        } else {
                            echo "Not info";
                            $mess = new Message();
                            $mess->subject = $message->getSubject();
                            $mess->uid = $uid;
                            $mess->body_html = $message->getHTMLBody();
                            $mess->body_text = $message->getTextBody();
                            $mess->header = $message->getHeaders();
                            $mess->message_date = $message->getdate();
                            $mess->mailbox_id = 2;
                            // dd($mess);
                            $mess->save();
                            foreach ($to as $mail) {
                                // dd($mail);
                                $address = new address;
                                $address->message_id = $mess->id;
                                $address->email = $mail->mail;
                                $address->type = 'to';
                                $address->save();
                            }
                            //dd($from, $to, $address);
                            foreach ($from as $mail) {
                                $address = new address;
                                $address->message_id = $mess->id;
                                $address->email = $mail->mail;
                                $address->type = 'from';
                                $address->save();
                            }
                            $dir = 'storage/files/temp';
                            if (!is_dir($dir)) {
                                mkdir($dir, 0777, true);
                            }

                            foreach ($attachments as $attachment) {
                                $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
                                $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
                                $attachment->save($path = $dir . '/', $filename);
                                $attach = new attachment();
                                $attach->message_id = $mess->id;
                                $attach->location = $filename;
                                $attach->mime_type = $attachment->getMimeType();
                                $attach->file_size = $attachment->getSize();
                                $attach->file_name = $attachment->getFilename();
                                $attach->save();
                            }
                        }
                    }
                }
            }
        }
        // $client2 = $cm->account('ananin');
        // //   dd($client);
        // //Connect to the IMAP Server
        // $client2->connect();

        // //Get all Mailboxes
        // /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        // $folders = $client2->getFolders(true, "INBOX");

        // foreach ($folders as $folder) {


        //     //Get all Messages of the current Mailbox $folder
        //     /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
        //     $mailbox = DB::table('mailboxes')->where('mailboxes.id', '=', 3)->first();
        //     $start_uid = $mailbox->last_message_uid + 1;
        //     $emailes = $folder->overview($sequence = "$start_uid:*");
        //     /** @var \Webklex\PHPIMAP\Message $message */
        //     $last_uid = -1;
        //     foreach ($emailes as $uid => $email) {
        //         // dd($email);
        //         echo "<br />" . $uid . "<br />";
        //         $last_uid = $uid;
        //         $mailbox = mailbox::where('id', '=', 3)->first();
        //         $mailbox->last_message_uid = $last_uid;
        //         $mailbox->save();
        //         $messages = $folder->messages()->where(['uid' => $uid])->get();
        //         // dd($messages, $uid);
        //         foreach ($messages as $message) {
        //             $subject = $message->getSubject();
        //             echo $subject . "<br/>";


        //             $attachments = $message->getAttachments();

        //             $from = $message->getFrom()->toarray();
        //             $to = $message->getTo()->toArray();
        //             $subject = $message->getSubject();
        //             $subject = trim($subject);
        //             $body_html = $message->getHTMLBody();
        //             $our_company = 3;
        //             // dd($message);


        //             if (mb_strpos($body_html, 'vsk.ru') !== false) {
        //                 $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 27);
        //             } elseif (mb_strpos($body_html, 'ingos') !== false) {
        //                 echo "Ingos";

        //                 $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid, 27);
        //             } elseif (mb_strpos($body_html, 'Югория') !== false) {
        //                 $this->ugor_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
        //             } else {
        //                 echo "Not info";
        //                 $mess = new Message();
        //                 $mess->subject = $message->getSubject();
        //                 $mess->uid = $uid;
        //                 $mess->body_html = $message->getHTMLBody();
        //                 $mess->body_text = $message->getTextBody();
        //                 $mess->header = $message->getHeaders();
        //                 $mess->message_date = $message->getdate();
        //                 $mess->mailbox_id = 3;
        //                 // dd($mess);
        //                 $mess->save();
        //                 foreach ($to as $mail) {
        //                     // dd($mail);
        //                     $address = new address;
        //                     $address->message_id = $mess->id;
        //                     $address->email = $mail->mail;
        //                     $address->type = 'to';
        //                     $address->save();
        //                 }
        //                 //dd($from, $to, $address);
        //                 foreach ($from as $mail) {
        //                     $address = new address;
        //                     $address->message_id = $mess->id;
        //                     $address->email = $mail->mail;
        //                     $address->type = 'from';
        //                     $address->save();
        //                 }
        //                 $dir = 'storage/files/temp';
        //                 if (!is_dir($dir)) {
        //                     mkdir($dir, 0777, true);
        //                 }

        //                 foreach ($attachments as $attachment) {
        //                     $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
        //                     $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
        //                     $attachment->save($path = $dir . '/', $filename);
        //                     $attach = new attachment();
        //                     $attach->message_id = $mess->id;
        //                     $attach->location = $filename;
        //                     $attach->mime_type = $attachment->getMimeType();
        //                     $attach->file_size = $attachment->getSize();
        //                     $attach->file_name = $attachment->getFilename();
        //                     $attach->save();
        //                 }
        //             }
        //         }
        //     }
        // }
        // $client3 = $cm->account('sharoiko');
        //   dd($client);
        //Connect to the IMAP Server
        // $client3->connect();

        //Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        // $folders = $client3->getFolders(true, "INBOX");
        // foreach ($folders as $folder) {

        //     //Get all Messages of the current Mailbox $folder
        //     /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
        //     $mailbox = DB::table('mailboxes')->where('mailboxes.id', '=', 4)->first();
        //     $start_uid = $mailbox->last_message_uid + 1;
        //     $emailes = $folder->overview($sequence = "$start_uid:*");
        //     /** @var \Webklex\PHPIMAP\Message $message */
        //     $last_uid = -1;
        //     // $emailes = $folder->messages()->UNSEEN()->get();

        //     foreach ($emailes as $uid => $email) {
        //         // dd($uid, $email);
        //         echo "<br />" . $uid . "<br />";
        //         $last_uid = $uid;
        //         $mailbox = mailbox::where('id', '=', 4)->first();
        //         $mailbox->last_message_uid = $last_uid;
        //         $mailbox->save();
        //         $messages = $folder->messages()->where(['uid' => $uid])->get();
        //         // dd($messages, $uid);
        //         foreach ($messages as $message) {
        //             $subject = $message->getSubject();
        //             echo $subject . "<br/>";


        //             $attachments = $message->getAttachments();

        //             $from = $message->getFrom()->toarray();
        //             $to = $message->getTo()->toArray();
        //             $subject = $message->getSubject();
        //             $subject = trim($subject);
        //             $body_html = $message->getHTMLBody();
        //             $our_company = 4;
        //             // dd($message);


        //             if (mb_strpos($body_html, 'vsk.ru') !== false) {
        //                 $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
        //             } elseif (mb_strpos($body_html, 'ingos') !== false) {
        //                 echo "Ingos";

        //                 $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
        //             } else {
        //                 echo "Not info";
        //                 $mess = new Message();
        //                 $mess->subject = $message->getSubject();
        //                 $mess->uid = $uid;
        //                 $mess->body_html = $message->getHTMLBody();
        //                 $mess->body_text = $message->getTextBody();
        //                 $mess->header = $message->getHeaders();
        //                 $mess->message_date = $message->getdate();
        //                 $mess->mailbox_id = 4;
        //                 // dd($mess);
        //                 $mess->save();
        //                 foreach ($to as $mail) {
        //                     // dd($mail);
        //                     $address = new address;
        //                     $address->message_id = $mess->id;
        //                     $address->email = $mail->mail;
        //                     $address->type = 'to';
        //                     $address->save();
        //                 }
        //                 //dd($from, $to, $address);
        //                 foreach ($from as $mail) {
        //                     $address = new address;
        //                     $address->message_id = $mess->id;
        //                     $address->email = $mail->mail;
        //                     $address->type = 'from';
        //                     $address->save();
        //                 }
        //                 $dir = 'storage/files/temp';
        //                 if (!is_dir($dir)) {
        //                     mkdir($dir, 0777, true);
        //                 }

        //                 foreach ($attachments as $attachment) {
        //                     $filename = pathinfo($attachment->getFilename(), PATHINFO_FILENAME);
        //                     $filename .= "_" . date('d-m-y_hms') . "." . $attachment->getExtension();
        //                     $attachment->save($path = $dir . '/', $filename);
        //                     $attach = new attachment();
        //                     $attach->message_id = $mess->id;
        //                     $attach->location = $filename;
        //                     $attach->mime_type = $attachment->getMimeType();
        //                     $attach->file_size = $attachment->getSize();
        //                     $attach->file_name = $attachment->getFilename();
        //                     $attach->save();
        //                 }
        //             }
        //         }
        //     }
        // }
    }
}