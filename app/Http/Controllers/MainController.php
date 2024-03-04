<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use App\Models\message;
use App\Models\application;
use App\Models\attachment;
use App\Models\User;
use App\Mail\user\appnotify;
use \ConvertApi\ConvertApi;
 

use Illuminate\Support\Facades\DB;
use Ibnuhalimm\LaravelPdfToHtml\Facades\PdfToHtml;

use Escarter\PopplerPhp\PdfToText;
use Escarter\PopplerPhp\getOutput;
use Mtownsend\XmlToArray\XmlToArray;
use function Laravel\Prompts\table;

class MainController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }
    public function contact()
    {
        return view('contact');
    }

    public function register()
    {
        return view('auth/register');
    }
    public function dashboard()
    {
        return view('dashboard');
    }

    public function user()
    {
        return view('user');
    }
    public function review()
    {
        $rewiews = new Contact();
        // dd($rewiew->all()) ;
        return view('review', ['reviews' => $rewiews->all()]);
    }
    public function new_application()
    { 
        $messages = message::where('is_parsed', null)->get();
        foreach ($messages->all() as $message) {
            $subject = $message->subject;
            $subject = trim($subject);
            $body_html = $message->body_html;
            if (mb_strpos($body_html, 'vsk.ru') !== false) {
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

                    continue;
                }

                // echo $app_number . '<br>';
                $start_pos_gnz = mb_strripos($subject, ' ');

                $model = mb_substr($subject, $finish_pos_app_number + 8, $start_pos_gnz - $finish_pos_app_number - 8);
                // echo $model . '<br>';
                $gnz = mb_substr($subject, $start_pos_gnz, iconv_strlen($subject) - $start_pos_gnz);
                echo $gnz . '<br>';
                $applications = application::firstorcreate(
                    ['numberInsuranceCompanyApplication' => $app_number, 'id_insuranceCompany' => 1],
                    [
                        'numberInsuranceCompanyApplication' => $app_number,
                        'id_insuranceCompany' => 1,
                        'CarModel' => $model,
                        'gnz' => $gnz,
                        'id_status' => 1

                    ]
                );

                $message->application_id = $applications->id;

                $message->save();

            }
            $message->is_parsed = true;
            $attachment=attachment::where('message_id', $message->id)->get();
            foreach ($attachment->all() as $att) {
                $att->id_application= $applications->id;

                $att->save();
            }
            //$user=User::find(2);
          //   Mail::to($user->email)->send(new appnotify($applications->id)); 
            $message->save();
        }
    }
    public function review_check(Request $request)
    {

        $valid = $request->validate([
            'subject' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        $rewiew = new Contact();
        $rewiew->email = $request->input('email');
        $rewiew->subject = $request->input('subject');
        $rewiew->message = $request->input('message');

        $rewiew->save();

        return redirect()->route('review');
    }


    public function message()
    {
        $messages = new message();
        // dd($rewiew->all()) ;
        return view('mail.index', ['messages' => $messages->all()]);
    }
    public function document($id)
    {
        $document = DB::table('attachments')
              ->where('id_application', '=', $id)
            ->get();
        // dd($document);
        return view('document', ['document' => $document], ['id' => $id]);
    }
    public function showdoc($id)
    {

        // $text = (new PdfToText('/opt/homebrew/bin/pdftotext'))
        //     ->setPdf('storage/files/' . $id)
        //     ->text();
        // echo $text;
        //  exec('pdftohtml storage/files/' . $id . ' -p -i -xml');
         exec('pdftohtml storage/files/' . $id . ' -p -i');
// ConvertApi::setApiSecret('Mrv6e6zPa9oTst7o');
// $result = ConvertApi::convert('pdf-extract', [
//         'File' => 'storage/files/'.$id,
//     ], 'pdf'
// );
// $result->saveFiles('storage/files/');

        $html = substr($id, 0, strlen($id) - 4) . 's.html';
        //  include('storage/files/' . $html);
        $file = file_get_contents('storage/files/' . $html);
        $file = strip_tags($file, ['br']);
        // echo $file;
        $start_number = mb_strpos($file, 'ЭКСПЕРТНОЕ ЗАКЛЮЧЕНИЕ');
        $number = mb_substr($file, $start_number + 35, 7);
        echo 'ЭКСПЕРТНОЕ ЗАКЛЮЧЕНИЕ' . $start_number . '<br>' . $number . '<br>';
        $inf_start = mb_strpos($file, 'Марка, модель');
        echo $inf_start . '<br /><br />';
        $inf_end = mb_strpos($file, 'Точное');
        echo $inf_end . '<br /><br />';
        $inf = mb_substr($file, $inf_start, $inf_end - $inf_start);
        // echo $inf;
        $inf_array = explode('<br/>', $inf);
        echo '<table border=1>';
        for ($i = 0; $i < count($inf_array) - 1; $i) {
            echo '<tr><td>' . $inf_array[$i] . '</td><td>' . $inf_array[$i + 1] . '</td></tr>';
            $i = $i + 2;
            if ($i == 18) {
                $i++;
            }
        }
        echo '</table>';


        //dd($inf_array);
        $sparepart_start = mb_strpos($file, 'износом, Рубли');
        echo $sparepart_start . ' ЗЧ_Старт<br /><br />';
        $sparepart_end = mb_strpos($file, 'ИТОГО');
        echo $sparepart_end . ' ЗЧ_Фин<br /><br />';
        $sparepart = mb_substr($file, $sparepart_start + 14, $sparepart_end - $sparepart_start + 14);
        //echo $sparepart;
        $sparepart_array = explode('<br/>', $sparepart);
        echo '<table border=1>';
        for ($i = 1; $i < count($sparepart_array) - 8; $i) {
            echo '<tr><td>' . $sparepart_array[$i] . '</td>
            <td>' . $sparepart_array[$i + 1] . '</td>
            <td>' . $sparepart_array[$i + 2] . '</td>
            <td>' . $sparepart_array[$i + 3] . '</td>
            <td>' . $sparepart_array[$i + 4] . '</td>
            <td>' . $sparepart_array[$i + 5] . '</td>
            <td>' . $sparepart_array[$i + 6] . '</td>
            <td>' . $sparepart_array[$i + 7] . '</td>
            </tr>';
            $i = $i + 8;
        }
        echo '</table>';
        $work_start = mb_strpos($file, 'н-час');
        echo $work_start . 'Раб_Старт<br /><br />';
        $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
        echo $work_end . 'Раб_фин<br /><br />';
        $work = mb_substr($file, $work_start + 5, $work_end - $work_start - 18);

        $work_array = explode('<br/>', $work);
        // dd($work_array);
        $iskl = [29, 74, 75, 76, 77, 78, 79];
        echo '<table border=1>';
        for ($i = 1; $i < count($work_array) - 4; $i) {
            foreach ($iskl as $k) {
                if ($i == $k) {
                    $i++;
                }
            }
            echo '<tr><td>' . $work_array[$i] . '</td>
            <td>' . $work_array[$i + 1] . '</td>
            <td>' . $work_array[$i + 2] . '</td>
            <td>' . $work_array[$i + 3] . '</td>
            </tr>';
            $i = $i + 4;
        }
        echo '</table>';

       
    }

    public function doc_edit(Request $request,$id,$doc_id)
    {
         $document = DB::table('attachments')
              ->where('id_application', '=', $id)
            ->get();
        // dd($document);
        $document_edit = DB::table('attachments')
            ->where('attachments.id', '=', $doc_id)
            ->first();
        // dd($document);
        //return view('doc_edit', ['document' => $document,'id' => $id, 'doc_id' => $doc_id]);
        $filename=$document_edit->file_name;
        $doc_ext=mb_substr($filename, mb_strripos($filename, '.') + 1, mb_strlen($filename) - mb_strripos($filename, '.'));
        $doc_name=mb_substr($filename, 0, mb_strlen($filename)-mb_strlen($doc_ext)-1);
        return view('document', ['document' => $document, 'doc_name'=> $doc_name, 'doc_ext'=>$doc_ext, 'document_edit'=>$document_edit], ['id' => $id]);
    }
    public function doc_update(Request $request,$id,$doc_id)
    {   
        $document = Attachment::findOrFail($doc_id);
        $document->file_name = $request->input('file_name').'.'.$request->input('file_ext');
        $document->save();
        return redirect()->route('document', ['id' => $id]);
    }
    public function doc_delete(Request $request,$id,$doc_id)
    {
        $document = Attachment::findOrFail($doc_id);
        Storage::delete('public/files/'.$document->location);
        $document->delete();
        return redirect()->route('document', ['id' => $id]);
    }
    public function doc_load($id)
    {
        return view('doc_load', ['id' => $id]);
    }
    public function doc_add(Request $request,$id)
    {
        if ($request->isMethod('post') && $request->file('userfile')) {

            $file = $request->file('userfile');
            // dd($file);
            $upload_folder = 'public/files';
            $filename = $file->getClientOriginalName(); // 
            $filename_new = uniqid("", false) . "." . mb_substr($filename, mb_strripos($filename, '.') + 1, mb_strlen($filename) - mb_strripos($filename, '.'));
            Storage::putFileAs($upload_folder, $file, $filename_new);
            $document = new Attachment();
            $document->file_name = $filename;
            $document->message_id = 0;
            $document->mime_type=$file->getMimeType();
            $document->id_application = $id;
            $document->file_size = $file->getSize();
            $document->location = $filename_new;
            $document->save();
        }
       
        return redirect()->route('document', ['id' => $id]);
    }
    public function test(request $request)
    {
        dd($request);
    }

}
