<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\message;
use App\Models\application;
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
                        'gnz' => $gnz

                    ]
                );

                $message->application_id = $applications->id;

                $message->save()
            }
            $message->is_parsed = true;
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

    public function applications()
    {
        $applications = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->get();
        // dd($applications);
        return view('applications', ['applications' => $applications],);
    }
    public function application($id)
    {
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->where('applications.id', '=', $id)
            ->first();
        // dd($application);
        return view('application.index', ['application' => $application], ['id' => $id]);
    }
    public function message()
    {
        $messages = new message();
        // dd($rewiew->all()) ;
        return view('mail.index', ['messages' => $messages->all()]);
    }
    public function document($id)
    {
        $document = DB::table('messages')
            ->join('attachments', 'attachments.message_id', '=', 'messages.id')
            ->select('attachments.*')
            ->where('messages.application_id', '=', $id)
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
        exec('pdftohtml storage/files/' . $id . ' -p -i -xml');
        exec('pdftohtml storage/files/' . $id . ' -p -i');

        $html = substr($id, 0, strlen($id) - 4) . 's.html';
        include('storage/files/' . $html);
        // $array = XmlToArray::convert($html);
        // //var_dump($html);
        $dom = new DOMDocument();

        //examples

        //methods to load HTML
        $dom->loadHTML($html_string);
        $dom->loadHTMLFile('path/to/htmlfile.html');

        //methods to load XML
        $dom->load('path/to/xmlfile.xml');
        $dom->loadXML($xml_string);

        $documentElement = $dom->documentElement; 
    }
}
