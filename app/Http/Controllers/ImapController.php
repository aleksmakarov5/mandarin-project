<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Mail\user\appnotify;
use \ConvertApi\ConvertApi;
use Illuminate\Support\Facades\Mail as Mail_send;




class ImapController extends Controller
{
    public function vsk_parse($subject, $email, $from, $to, $attachments, $our_company)
    {


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
        if (!$app_number) {
        } else {
            // echo $app_number . '<br>';
            $start_pos_gnz = mb_strripos($subject, ' ');

            $model = mb_substr($subject, $finish_pos_app_number + 8, $start_pos_gnz - $finish_pos_app_number - 8);
            // echo $model . '<br>';
            $gnz = mb_substr($subject, $start_pos_gnz, iconv_strlen($subject) - $start_pos_gnz);
            echo $gnz . '<br>';


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
            $dir = 'storage/files/' . $applications->id;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
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
        }
    }
    public function ingos_parse($subject, $email, $from, $to, $attachments, $our_company)
    {
        echo $subject;

        $array_subject = explode(' ', $subject);
        $gnz = '';
        $app_number = '';

        foreach ($array_subject as $item) {
            if (preg_match("/\D[0-9]{3}\D\D[0-9]{3}/", $item) == 1 || preg_match("/\D[0-9]{3}\D\D[0-9]{2}/", $item) == 1) {
                $gnz = $item;
            }
            if (preg_match("/[0-9]{7}/", $item) == 1 && strlen($item) < 8) {
                $app_number = $item;
            }
        }


        echo $gnz . '<br>';
        echo $app_number . '<br>';


        $applications = application::firstorcreate(
            ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 3, 'our_company' => $our_company],
            [
                'numberInsuranceCompanyApplication' => $app_number,
                'id_insuranceCompany' => 3,
                'gnz' => $gnz,
                'CarModel' => 'model',
                'id_status' => 1,
                'our_company' => $our_company
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
        $dir = 'storage/files/' . $applications->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
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
        $folders = $client->getFolders();

        //Loop through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach ($folders as $folder) {

            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $emailes = $folder->messages()->UNSEEN()->get();
            //dd($folder);
            /** @var \Webklex\PHPIMAP\Message $message */
            foreach ($emailes as $email) {

                //dd($email);
                $email->setFlag('Seen');
                $attachments = $email->getAttachments();

                $from = $email->getFrom()->toarray();
                $to = $email->getTo()->toArray();
                $subject = $email->getSubject();
                $subject = trim($subject);
                $body_html = $email->getHTMLBody();
                $our_company = 1;

                if (mb_strpos($body_html, 'vsk.ru') !== false) {
                    $this->vsk_parse($subject, $email, $from, $to, $attachments, $our_company);
                }
                if (mb_strpos($body_html, 'ingos.ru') !== false) {
                    echo "Ingos";

                    $this->ingos_parse($subject, $email, $from, $to, $attachments, $our_company);
                }
            }
        }

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