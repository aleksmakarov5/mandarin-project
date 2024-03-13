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




class ImapController extends Controller
{
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
                $message = new Message();

                //dd($email);
                $email->setFlag('Seen');
                $attachments = $email->getAttachments();
                $message->subject = $email->getSubject();
                $message->body_html = $email->getHTMLBody();
                $message->body_text = $email->getTextBody();
                $message->header = $email->getHeaders();
                $message->message_date = $email->getdate();
                $from = $email->getFrom()->toarray();
                $to = $email->getTo()->toArray();
                // $adressto = $to->toArray();

                // $address = $to->getAddress();
                // dd($message);
                //echo $values[2]->mail;
                // echo count($to);
                $message->save();
                foreach ($attachments as $attachment) {
                    $filename =  uniqid("", false) . "." . $attachment->getExtension();
                    $status = $attachment->save($path = "storage/files/", $filename);
                    $attach = new attachment();
                    $attach->message_id = $message->id;
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
            }
        }
    }
}
