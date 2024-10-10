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
use Carbon\Carbon;
use Tochka\Calendar\WorkCalendar;




class ImapController extends Controller
{
    public function vsk_parse($subject, $email, $from, $to, $attachments, $our_company, $uid)
    {
        $app_number = ' ';
        $model =  ' ';
        $vin =  ' ';
        $gnz = ' ';

        $start_pos_app_number = strripos($subject, ':');
        if (
            $start_pos_app_number === false
        ) {
            $start_pos_app_number;
        } else {
            $start_pos_app_number++;
        }
        $finish_pos_app_number = mb_strripos($subject, '_');

        $app_number = mb_substr($subject, $start_pos_app_number, $finish_pos_app_number - $start_pos_app_number);
        $app_number = trim($app_number);
        $app_number = preg_replace('/[^0-9]/', '', $app_number);
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
                $app_number = mb_substr($subject, $start_pos_app_number, 7);
                $app_number = trim($app_number);
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
            $app_number = $app_number ? $app_number : ' ';
            $model = $model ? $model : ' ';
            $vin = $vin ? $vin : ' ';
            $gnz = $gnz ? $gnz : ' ';

            //dd($start_pos_app_number, $subject, $start_pos_gnz, $app_number, $model, $vin, $gnz);
            $applications = application::firstorcreate(
                ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 1, 'our_company' => $our_company],
                [
                    'numberInsuranceCompanyApplication' => $app_number,
                    'id_insuranceCompany' => 1,
                    'CarModel' => $model,
                    'gnz' => $gnz,
                    'id_status' => 1,
                    'vin' => $vin,
                    'ub_type' => true,
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
            $message->application_id = $applications->id;
            $id = $applications->id;
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
            $message->is_parsed = true;
            $message->mailbox_id = $our_company;
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

                        $notify->save();
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
                        }


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
            Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
            $notify_id = 0;
            $users = user::where('GroupId', '=', 2)->orWhere('GroupId', '=', 1)->get()->all();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify->save();
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
                }
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
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
                    'id_status' => 1
                ]
            );
            // dd($applications);
            $message = new Message();
            $message->subject = $email->getSubject();
            $message->body_html = $email->getHTMLBody();
            $message->body_text = $email->getTextBody();
            $message->header = $email->getHeaders();
            $message->message_date = $email->getdate();
            $message->application_id = $applications->id;
            $id = $applications->id;
            $history = new History;
            $history->id_application = $applications->id;
            $history->id_status = 1;
            $history->id_user = 2;
            $history->save();
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

                        $notify->save();
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
                        }


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
            Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
            $notify_id = 0;
            $users = user::where('GroupId', '=', 2)->orWhere('GroupId', '=', 1)->get()->all();
            foreach ($users as $user) {
                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 1;
                $notify->link = "/mail/";
                $notify->save();
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
                }
            }

            $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')->first();
            broadcast(new MessageSent($notification));
        }
    }
    public function ingos_parse($subject, $email, $from, $to, $attachments, $our_company, $uid)
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
            $app_number =  $app_number . "/24";
        }
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
                'id_status' => 1,
                'vin' => " ",
                'ub_type' => false,
                'our_company' => $our_company
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
        $message->application_id = $applications->id;
        $id = $applications->id;
        $history = new History;
        $history->id_application = $applications->id;
        $history->id_status = 1;
        $history->id_user = 2;
        $history->save();
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

                    $notify->save();
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
                    }


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
        Mail_send::to('aleksmakarov5@yandex.ru')->queue(new appnotify($id));
        $notify_id = 0;
        $users = user::where('GroupId', '=', 2)->orWhere('GroupId', '=', 1)->get()->all();
        foreach ($users as $user) {
            $notify = new notify;
            $notify->id_user = $user->id;
            $notify->id_application = $id;
            $notify->id_text = 1;
            $notify->link = "/mail/";
            $notify->save();
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
            }
        }

        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
    }
    public function recieve()
    {
        $cm = new ClientManager('config/imap.php');

        /** @var \Webklex\PHPIMAP\Client $client */
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

                    if (mb_strpos($body_html, 'vsk.ru') !== false) {
                        $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                    } elseif (mb_strpos($body_html, 'ingos') !== false) {
                        echo "Ingos";

                        $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
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
            if ($last_uid != (-1)) {
                $mailbox = mailbox::where('id', '=', 1)->first();
                $mailbox->last_message_uid = $last_uid;
                $mailbox->save();
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

                echo $uid . "<br />";
                $last_uid = $uid;
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
                    $our_company = 2;
                    echo mb_strpos($body_html, 'vsk.ru') . ' ВСК<br>';
                    echo mb_strpos($body_html, 'ingos') . ' ИНГОС<br>';
                    // dd($message);


                    if (mb_strpos($body_html, 'vsk.ru') !== false) {
                        $this->vsk_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                    } elseif (mb_strpos($body_html, 'ingos') !== false) {
                        echo "Ingos";

                        $this->ingos_parse($subject, $message, $from, $to, $attachments, $our_company, $uid);
                    } else {
                        $mess = new Message();
                        $mess->subject = $message->getSubject();
                        $mess->uid = $uid;
                        $mess->body_html = $message->getHTMLBody();
                        $mess->body_text = $message->getTextBody();
                        $mess->header = $message->getHeaders();
                        $mess->message_date = $message->getdate();
                        $mess->mailbox_id = 2;
                        dd($mess);
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
                    }
                }
            }
            if ($last_uid != (-1)) {
                $mailbox = mailbox::where('id', '=', 2)->first();
                $mailbox->last_message_uid = $last_uid;
                $mailbox->save();
            }
        }
        // dd($emailes);

        // // $client1 = $cm->account('makarov');
        // //   dd($client);
        // //Connect to the IMAP Server
        // $client1->connect();

        // //Get all Mailboxes
        // /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        // $folders = $client1->getFolders();

        // //Loop through every Mailbox
        // /** @var \Webklex\PHPIMAP\Folder $folder */
        // foreach ($folders as $folder) {

        //     //Get all Messages of the current Mailbox $folder
        //     /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
        //     $emailes = $folder->messages()->UNSEEN()->get();
        //     //dd($folder);
        //     /** @var \Webklex\PHPIMAP\Message $message */
        //     foreach ($emailes as $email) {

        //         //dd($email);
        //         $email->setFlag('Seen');
        //         $attachments = $email->getAttachments();

        //         $from = $email->getFrom()->toarray();
        //         $to = $email->getTo()->toArray();
        //         $subject = $email->getSubject();
        //         $subject = trim($subject);
        //         $body_html = $email->getHTMLBody();
        //         $our_company = 2;

        //         if (mb_strpos($body_html, 'vsk.ru') !== false) {
        //             $this->vsk_parse($subject, $email, $from, $to, $attachments, $our_company);
        //         }
        //     }
        // }
    }
    public function send()
    {
        $data["email"] = "aleksmakarov5@yandex.ru";
        $data["title"] = "DEMO sending";
        $data["body"] = "This is Demo";

        $location = ['65ebcb84efdee.pdf', '65d42a55af4ce.pdf', '65d42a55afeac.zip', '65d42a55b3dab.png'];
        $files = [];
        for ($i = 0; $i < count($location); $i++) {
            $files[] = public_path('/storage/files/' . $location[$i]);
        }


        Mail_send::mailer('makarov')->send('email.myTestMail', $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["email"])
                ->from('makarov@dvina-trust.ru', 'Двина')
                ->subject('Отправка с Двины');
            foreach ($files as $file) {
                $message->attach($file);
            }
        });
        Mail_send::send('email.myTestMail', $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject('Отправка с Мандарина');
            foreach ($files as $file) {
                $message->attach($file);
            }
        });

        dd('Mail sent successfully');
    }
}
