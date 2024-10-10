<?php

namespace App\Http\Controllers;

use App\Models\doc_status;
use App\Models\OurCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use App\Models\message;
use App\Models\application;
use App\Models\attachment;
use App\Models\User;
use App\Models\history;
use App\Jobs\QueueSenderEmФail;
use App\Mail\user\appnotify;
use \ConvertApi\ConvertApi;
use \ZanySoft\Zip\Facades\Zip;
use Selective\Rar\RarFileReader;
use SplFileObject;
use Archive7z\Archive7z;
use \CloudConvert\Laravel\Facades\CloudConvert;
use \CloudConvert\Models\Job;
use \CloudConvert\Models\Task;

use Illuminate\Support\Facades\DB;
use Ibnuhalimm\LaravelPdfToHtml\Facades\PdfToHtml;
use \PhpOffice\PhpWord\IOFactory;
use \Golchha21\ReSmushIt\Facades\Optimize;

use Escarter\PopplerPhp\PdfToText;
use Escarter\PopplerPhp\getOutput;
use Mtownsend\XmlToArray\XmlToArray;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{


    public function home()
    {
        if (isset(Auth::user()->id)) {
            session(['rout' => 'applications']);
            return redirect()->route('applications');
        } else {
            session(['rout' => 'home']);
            return view('home');
        }
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
        session(['rout' => 'review']);
        // dd($rewiew->all()) ;
        return view('review', ['reviews' => $rewiews->all()]);
    }
    public function new_application() {}
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
        // $messages = DB::table('messages')
        //     ->get()->sortDesc();
        // // dd($messages);
        // foreach ($messages as $message) {
        //     $message->attachment = DB::table('attachments')
        //         ->where('message_id', '=', $message->id)
        //         ->get();
        //     $message->from = DB::table('addresses')
        //         ->where('message_id', '=', $message->id)
        //         ->where('type', '=', 'from')
        //         ->first();
        //     $message->to = DB::table('addresses')
        //         ->where('message_id', '=', $message->id)
        //         ->where('type', '=', 'to')
        //         ->get();
        // }
        $our_companies = new OurCompany;

        session(['rout' => 'message']);
        // dd($rewiew->all()) ;
        return view('mail.index', ['our_companies' => $our_companies->all()]);
    }
    public function mailbox($type, $id, $message_id)
    {
        $mail = '';
        $body = "";
        if ($message_id != 0) {
            $ms = message::where('id', '=', $message_id)->get();
            // dd($mail);
            foreach ($ms as $m) {
                $m->is_ready = true;
                $m->save();
            }
            // dd($mail);
            $mail = message::where('id', '=', $message_id)->first();

            if (isset($mail->id)) {

                $mail->from = DB::table('addresses')
                    ->where('message_id', '=', $message_id)
                    ->where('type', '=', 'from')
                    ->first();
                $mail->attachment = DB::table('attachments')
                    ->where('message_id', '=', $mail->id)
                    ->get();
                $body = $mail->body_html;

                $body_start = mb_strpos($body, '<body');
                $body_end = mb_strpos($body, '</body>');
                if ($body_start) {
                    $body = mb_substr($body, $body_start, $body_end - $body_start);
                }
                $body = str_replace('color', '', $body);

                $body = str_replace('font-size:', '', $body);
                $body = str_replace('<base target="_self" href="https://e.mail.ru/">', '', $body);
                if (isset($mail->application_id)) {
                    $body = str_replace('cid:', '/storage/files/' . $mail->application_id . '/', $body);
                    $body = str_replace('gif', 'gif"', $body);
                    $body = str_replace('png', 'png"', $body);
                    $loc = Attachment::where('message_id', '=', $mail->id)
                        ->where('file_name', 'like', '%image001.png%')
                        ->first();
                    $location = isset($loc->location) ? $loc->location : '';
                    $body = str_replace('image001.png', $location, $body);
                    $start = mb_strpos('png', $body) ? 2 : 1;
                    for ($i = $start; $i < 10; $i++) {
                        $loc = Attachment::where('message_id', '=', $mail->id)
                            ->where('file_name', 'like', '%image00' . $i . '.gif%')
                            ->first();
                        $location = isset($loc->location) ? $loc->location : '';
                        $body = str_replace('image00' . $i . '.gif', $location, $body);
                    }
                }
                // dd($mail, $body);
            }
        }
        $messages = DB::table('messages')->where('mailbox_id', '=', $id)
            ->get()->sortDesc();
        // dd($messages);
        foreach ($messages as $message) {
            $message->attachment = DB::table('attachments')
                ->where('message_id', '=', $message->id)
                ->get();
            $message->from = DB::table('addresses')
                ->where('message_id', '=', $message->id)
                ->where('type', '=', 'from')
                ->first();
            $message->to = DB::table('addresses')
                ->where('message_id', '=', $message->id)
                ->where('type', '=', 'to')
                ->get();
        }
        // dd($mail);
        session(['rout' => 'message']);
        // dd($rewiew->all()) ;
        return view('mail.show', [
            'messages' => $messages->all(),
            'type' => $type,
            'message_id' => $message_id,
            'body' => $body,
            'company_id' => $id,
            'mail' => $mail,
            'id' => $id
        ]);
    }

    public function document(request $request, $id)
    {
        $hide_menu = $request->get('hide_menu');
        $document = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        $doc_statuses = new doc_status();
        $def = DB::table('applications')
            ->where('id', '=', $id)
            ->select('def')
            ->first();


        // dd($doc_statuses->all());
        return view('document', ['document' => $document, 'doc_statuses' => $doc_statuses->all(), 'def' => $def->def, 'hide_menu' => $hide_menu], ['id' => $id]);
    }
    public function showdoc($id, $locations)
    {

        // $text = (new PdfToText('/opt/homebrew/bin/pdftotext'))
        //     ->setPdf('storage/files/' . $id)
        //     ->text();
        // echo $text;
        //  exec('pdftohtml storage/files/' . $id . ' -p -i -xml');
        //  exec('pdftohtml storage/files/' . $id . ' -p -i');
        // ConvertApi::setApiSecret('Mrv6e6zPa9oTst7o');
        // $result = ConvertApi::convert('pdf-extract', [
        //         'File' => 'storage/files/'.$id,
        //     ], 'pdf'
        // );
        // $result->saveFiles('storage/files/');

        // $html = substr($id, 0, strlen($id) - 4) . 's.html';
        // //  include('storage/files/' . $html);
        // $file = file_get_contents('storage/files/' . $html);
        // $file = strip_tags($file, ['br']);
        // // echo $file;
        // $start_number = mb_strpos($file, 'ЭКСПЕРТНОЕ ЗАКЛЮЧЕНИЕ');
        // $number = mb_substr($file, $start_number + 35, 7);
        // echo 'ЭКСПЕРТНОЕ ЗАКЛЮЧЕНИЕ' . $start_number . '<br>' . $number . '<br>';
        // $inf_start = mb_strpos($file, 'Марка, модель');
        // echo $inf_start . '<br /><br />';
        // $inf_end = mb_strpos($file, 'Точное');
        // echo $inf_end . '<br /><br />';
        // $inf = mb_substr($file, $inf_start, $inf_end - $inf_start);
        // // echo $inf;
        // $inf_array = explode('<br/>', $inf);
        // echo '<table border=1>';
        // for ($i = 0; $i < count($inf_array) - 1; $i) {
        //     echo '<tr><td>' . $inf_array[$i] . '</td><td>' . $inf_array[$i + 1] . '</td></tr>';
        //     $i = $i + 2;
        //     if ($i == 18) {
        //         $i++;
        //     }
        // }
        // echo '</table>';


        // //dd($inf_array);
        // $sparepart_start = mb_strpos($file, 'износом, Рубли');
        // echo $sparepart_start . ' ЗЧ_Старт<br /><br />';
        // $sparepart_end = mb_strpos($file, 'ИТОГО');
        // echo $sparepart_end . ' ЗЧ_Фин<br /><br />';
        // $sparepart = mb_substr($file, $sparepart_start + 14, $sparepart_end - $sparepart_start + 14);
        // //echo $sparepart;
        // $sparepart_array = explode('<br/>', $sparepart);
        // echo '<table border=1>';
        // for ($i = 1; $i < count($sparepart_array) - 8; $i) {
        //     echo '<tr><td>' . $sparepart_array[$i] . '</td>
        //     <td>' . $sparepart_array[$i + 1] . '</td>
        //     <td>' . $sparepart_array[$i + 2] . '</td>
        //     <td>' . $sparepart_array[$i + 3] . '</td>
        //     <td>' . $sparepart_array[$i + 4] . '</td>
        //     <td>' . $sparepart_array[$i + 5] . '</td>
        //     <td>' . $sparepart_array[$i + 6] . '</td>
        //     <td>' . $sparepart_array[$i + 7] . '</td>
        //     </tr>';
        //     $i = $i + 8;
        // }
        // echo '</table>';
        // $work_start = mb_strpos($file, 'н-час');
        // echo $work_start . 'Раб_Старт<br /><br />';
        // $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
        // echo $work_end . 'Раб_фин<br /><br />';
        // $work = mb_substr($file, $work_start + 5, $work_end - $work_start - 18);

        // $work_array = explode('<br/>', $work);
        // // dd($work_array);
        // $iskl = [29, 74, 75, 76, 77, 78, 79];
        // echo '<table border=1>';
        // for ($i = 1; $i < count($work_array) - 4; $i) {
        //     foreach ($iskl as $k) {
        //         if ($i == $k) {
        //             $i++;
        //         }
        //     }
        //     echo '<tr><td>' . $work_array[$i] . '</td>
        //     <td>' . $work_array[$i + 1] . '</td>
        //     <td>' . $work_array[$i + 2] . '</td>
        //     <td>' . $work_array[$i + 3] . '</td>
        //     </tr>';
        //     $i = $i + 4;
        // }
        // echo '</table>';


        $content = '';
        $source = 'storage/files/' . $locations;

        $objReader = IOFactory::createReader('Word2007');

        $phpWord = $objReader->load($source);

        $body = '';
        foreach ($phpWord->getSections() as $section) {
            $arrays = $section->getElements();

            foreach ($arrays as $e) {
                if (get_class($e) === 'PhpOffice\PhpWord\Element\TextRun') {
                    foreach ($e->getElements() as $text) {

                        $font = $text->getFontStyle();

                        $size = $font->getSize() / 10;
                        $bold = $font->isBold() ? 'font-weight:700;' : '';
                        $color = $font->getColor();
                        $fontFamily = $font->getName();

                        $body .= '<span style="font-size:' . $size . 'em;font-family:' . $fontFamily . '; ' . $bold . '; color:#' . $color . '">';
                        $body .= $text->getText() . '</span>';
                    }
                } else if (get_class($e) === 'PhpOffice\PhpWord\Element\TextBreak') {
                    $body .= '<br />';
                } else if (get_class($e) === 'PhpOffice\PhpWord\Element\Table') {
                    $body .= '<table border="2px">';

                    $rows = $e->getRows();

                    foreach ($rows as $row) {
                        $body .= '<tr>';

                        $cells = $row->getCells();
                        foreach ($cells as $cell) {
                            $body .= '<td style="width:' . $cell->getWidth() . '">';
                            $celements = $cell->getElements();
                            foreach ($celements as $celem) {
                                if (get_class($celem) === 'PhpOffice\PhpWord\Element\Text') {
                                    $body .= $celem->getText();
                                } else if (get_class($celem) === 'PhpOffice\PhpWord\Element\TextRun') {
                                    foreach ($celem->getElements() as $text) {
                                        $body .= $text->getText();
                                    }
                                }
                            }
                            $body .= '</td>';
                        }

                        $body .= '</tr>';
                    }

                    $body .= '</table>';
                } else {
                    $body .= $e->getText();
                }
            }

            break;
        }

        echo $body;
    }

    public function doc_edit(Request $request, $id, $doc_id)
    {
        $document = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        // dd($document);
        $document_edit = DB::table('attachments')
            ->where('attachments.id', '=', $doc_id)
            ->first();
        $def = DB::table('applications')
            ->where('id', '=', $id)
            ->select('def')
            ->first();

        $doc_statuses = new doc_status();
        // dd($document);
        //return view('doc_edit', ['document' => $document,'id' => $id, 'doc_id' => $doc_id]);
        $filename = $document_edit->file_name;
        $doc_ext = mb_substr($filename, mb_strripos($filename, '.') + 1, mb_strlen($filename) - mb_strripos($filename, '.'));
        $doc_name = mb_substr($filename, 0, mb_strlen($filename) - mb_strlen($doc_ext) - 1);
        return view('document', ['document' => $document, 'doc_name' => $doc_name, 'doc_ext' => $doc_ext, 'document_edit' => $document_edit, 'doc_statuses' => $doc_statuses->all(), 'def' => $def->def], ['id' => $id]);
    }
    public function doc_update(Request $request, $id, $doc_id)
    {
        $document = Attachment::findOrFail($doc_id);
        $document->file_name = $request->input('file_name') . '.' . $request->input('file_ext');
        $document->save();
        return redirect()->route('document', ['id' => $id]);
    }
    public function doc_delete(Request $request, $id, $doc_id)
    {

        $document = Attachment::findOrFail($doc_id);
        if (pathinfo($document->location, PATHINFO_EXTENSION) == 'zip') {
            // echo 'zip';
            // dd(pathinfo($document->location, PATHINFO_EXTENSION));
            // Storage::delete('public/files/' . $id . '/' . pathinfo($document->location, PATHINFO_FILENAME));
            $att = scandir('storage/files/' . $id . '/' . pathinfo($document->location, PATHINFO_FILENAME));
            foreach ($att as $file) {
                if ($file != '.' && $file != '..') {
                    unlink('storage/files/' . $id . '/' . pathinfo($document->location, PATHINFO_FILENAME) . '/' . $file);
                }
            }
            rmdir('storage/files/' . $id . '/' . pathinfo($document->location, PATHINFO_FILENAME));
        }
        Storage::delete('public/files/' . $id . '/' . $document->location);
        $document->delete();
        return redirect()->route('document', ['id' => $id]);
    }
    public function doc_load($id)
    {
        return view('doc_load', ['id' => $id]);
    }
    public function doc_add(Request $request, $id)
    {
        if ($request->isMethod('post') && $request->file('userfile')) {

            // $file = $request->file('userfile');
            // dd($request->file('userfile'));
            foreach ($request->file('userfile') as $file) {
                $upload_folder = '' . $id;
                $filenameWithExt  = $file->getClientOriginalName(); // 
                // Имя и расширение файла
                // Только оригинальное имя файла
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Расширение
                $extention = $file->getClientOriginalExtension();
                // Путь для сохранения
                $fileNameToStore = $filename . "_" . date('d-m-y_hms') . "." . $extention;
                // Сохраняем файл
                $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                $filename = 'storage/files/' . $id . '/' . $fileNameToStore;
                Optimize::path($filename);
                // ПРОВЕРКА НА ИЗОБРАЖЕНИЕ
                // $size = getimagesize($filename); // если это изображение и у него определён размер, то продолжаем
                // if ($size) {

                //     // ОПРЕДЕЛЯЕМ МАКСИМАЛЬНЫЕ ШИРИНУ И ВЫСОТУ ИЗОБРАЖЕНИЯ
                //     $width = 500;
                //     $height = 500;

                //     // РАБОТАЕМ КОРРЕКТНО. ОПРЕДЕЛЯЕМ ТИП
                //     header("Content-type: {$size['mime']}");

                //     // ПОЛУЧАЕМ НОВЫЕ РАЗМЕРЫ
                //     list($width_orig, $height_orig) = getimagesize($filename);

                //     if (
                //         $width && ($width_orig < $height_orig)
                //     ) {
                //         $width = ($height / $height_orig) * $width_orig;
                //     } else {
                //         $height = ($width / $width_orig) * $height_orig;
                //     }

                //     // ПЕРЕСОХРАНЯЕМ ИЗОБРАЖЕНИЕ
                //     $image_p = imagecreatetruecolor($width, $height);
                //     $image = imagecreatefromjpeg($filename);
                //     imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                //     // СОЗДАЁМ
                //     $img_name =  'storage/files/' . $id . '/' . pathinfo($filenameWithExt, PATHINFO_FILENAME) . date('d-m-y_hms') . 'compressed.' . $extention;
                //     imagejpeg($image_p, $img_name, 100);

                //     // УДАЛЯЕМ ИСХОДНИК - НУЖЕН АДРЕС. НАПРИМЕР, images/img001.jpg
                //     //unlink($filename);
                // }
                // $filename_new = uniqid("", false) . "." . mb_substr($filename, mb_strripos($filename, '.') + 1, mb_strlen($filename) - mb_strripos($filename, '.'));
                // Storage::putFileAs($upload_folder, $file, $filename_new);
                $document = new Attachment();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $document->file_name = $filename;
                $document->message_id = 0;
                $document->mime_type = $file->getMimeType();
                $document->id_application = $id;
                $document->file_size = $file->getSize();
                $document->location = $fileNameToStore;
                // dd($document);
                $document->save();
            }
        }

        return redirect()->route('document', ['id' => $id]);
    }
    public function priemka_add(Request $request, $id)
    {
        if ($request->isMethod('post') && $request->file('userfile')) {

            // $file = $request->file('userfile');
            // dd($request->file('userfile'));
            if (isset($request->load_name)) {

                $dir = 'storage/files/' . $id . '/' . $request->load_location . '';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $zip_path = 'storage/files/' . $id . '/' . $request->load_location . '.zip';
                $zip_bool = true;
                if (file_exists($zip_path)) {
                    $zip = Zip::open($zip_path);
                    $zip_bool = false;
                } else {
                    $zip = Zip::create($zip_path);
                }
                foreach ($request->file('userfile') as $file) {
                    if ($file->getClientOriginalExtension() == 'pdf') {
                        // dd($file);
                        $upload_folder = '' . $id;
                        $filenameWithExt = $file->getClientOriginalName(); // 
                        // Имя и расширение файла
                        // Только оригинальное имя файла
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        // Расширение
                        $extention = $file->getClientOriginalExtension();
                        // Путь для сохранения
                        $fileNameToStore = $filename . "_" . date('d-m-y_hms') . "." . $extention;
                        // Сохраняем файл
                        $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                        $filename = 'storage/files/' . $id . '/' . $fileNameToStore;
                        // dd($file);

                        $document = new Attachment();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $document->file_name = $filename;
                        $document->message_id = 0;
                        $document->mime_type = $file->getMimeType();
                        $document->id_application = $id;
                        $document->file_size = $file->getSize();
                        $document->id_status = $request->status_id;
                        $document->location = $fileNameToStore;
                        // dd($document);
                        $document->save();
                    } else {
                        $upload_folder = '' . $id . '/' . $request->load_location;
                        $filenameWithExt = $file->getClientOriginalName(); // 
                        // Имя и расширение файла
                        // Только оригинальное имя файла
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        // Расширение
                        $extention = $file->getClientOriginalExtension();
                        // Путь для сохранения
                        $fileNameToStore = $filename . "_" . date('d-m-y_hms') . "." . $extention;
                        // Сохраняем файл
                        $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                        $filename = 'storage/files/' . $upload_folder . '/' . $fileNameToStore;

                        // ПРОВЕРКА НА ИЗОБРАЖЕНИЕ

                        $size = getimagesize($filename); // если это изображение и у него определён размер, то продолжаем
                        if ($size) {
                            Optimize::path($filename);
                            $document = new Attachment();
                            $zip->setpath($dir)->add($fileNameToStore);
                            // $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        }
                    }
                }


                $zip->close();
                if ($zip_bool) {
                    $document->file_name = $request->load_name;
                    $document->message_id = 0;
                    $document->mime_type = 'application/zip-compressed';
                    $document->id_application = $id;
                    $document->file_size = filesize($dir . '/' . $fileNameToStore);
                    $document->id_status = $request->status_id;
                    $document->location = $request->load_location . '.zip';
                    // dd($document);
                    $document->save();
                }
            } else {
                foreach ($request->file('userfile') as $file) {

                    // dd($file);
                    $upload_folder = '' . $id;
                    $filenameWithExt = $file->getClientOriginalName(); // 
                    // Имя и расширение файла
                    // Только оригинальное имя файла
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    // Расширение
                    $extention = $file->getClientOriginalExtension();
                    // Путь для сохранения
                    $fileNameToStore = $filename . "_" . date('d-m-y_hms') . "." . $extention;
                    // Сохраняем файл
                    $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                    $filename = 'storage/files/' . $id . '/' . $fileNameToStore;
                    // dd($file);

                    $document = new Attachment();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $document->file_name = $filename;
                    $document->message_id = 0;
                    $document->mime_type = $file->getMimeType();
                    $document->id_application = $id;
                    $document->file_size = $file->getSize();
                    $document->id_status = $request->status_id;
                    $document->location = $fileNameToStore;
                    // dd($document);
                    $document->save();
                }
            }




            //     // ОПРЕДЕЛЯЕМ МАКСИМАЛЬНЫЕ ШИРИНУ И ВЫСОТУ ИЗОБРАЖЕНИЯ
            //     $width = 500;
            //     $height = 500;

            //     // РАБОТАЕМ КОРРЕКТНО. ОПРЕДЕЛЯЕМ ТИП
            //     header("Content-type: {$size['mime']}");

            //     // ПОЛУЧАЕМ НОВЫЕ РАЗМЕРЫ
            //     list($width_orig, $height_orig) = getimagesize($filename);

            //     if (
            //         $width && ($width_orig < $height_orig)
            //     ) {
            //         $width = ($height / $height_orig) * $width_orig;
            //     } else {
            //         $height = ($width / $width_orig) * $height_orig;
            //     }

            //     // ПЕРЕСОХРАНЯЕМ ИЗОБРАЖЕНИЕ
            //     $image_p = imagecreatetruecolor($width, $height);
            //     $image = imagecreatefromjpeg($filename);
            //     imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

            //     // СОЗДАЁМ
            //     $img_name =  'storage/files/' . $id . '/' . pathinfo($filenameWithExt, PATHINFO_FILENAME) . date('d-m-y_hms') . 'compressed.' . $extention;
            //     imagejpeg($image_p, $img_name, 100);

            //     // УДАЛЯЕМ ИСХОДНИК - НУЖЕН АДРЕС. НАПРИМЕР, images/img001.jpg
            //     //unlink($filename);
            // }
            // $filename_new = uniqid("", false) . "." . mb_substr($filename, mb_strripos($filename, '.') + 1, mb_strlen($filename) - mb_strripos($filename, '.'));
            // Storage::putFileAs($upload_folder, $file, $filename_new);


        }
        $application = application::findOrFail($id);
        switch ($request->status_id) {
            case 1:
                $application->id_status = 12;
                break;
            case 10:
                $application->id_status = 16;
                break;
        }
        $application->save();
        return redirect()->route('document', ['id' => $id]);
    }
    public function test(request $request)
    {
        dd($request);
    }
    public function gallery(request $request, $id, $location, $name)
    {
        $hide_menu = $request->get('hide_menu');
        $location = str_replace('%20', ' ', $location);
        $name = str_replace('%20', ' ', $name);
        $dir = 'storage/files/' . $id . '/' . pathinfo($location, PATHINFO_FILENAME);
        chmod($dir . "." . pathinfo($location, PATHINFO_EXTENSION), 0777);

        if (!is_dir($dir)) {
            if (pathinfo($location, PATHINFO_EXTENSION) == 'zip') {
                $zip = Zip::open($dir . '.' . pathinfo($location, PATHINFO_EXTENSION));
                $zip->extract($dir);
                $zip->close();
            } else if (pathinfo($location, PATHINFO_EXTENSION) == 'rar') {
                mkdir($dir, 0777, true);

                // dd(pathinfo($location, PATHINFO_EXTENSION));
                // echo ("unrar x " . $dir . "." . pathinfo($location, PATHINFO_EXTENSION) . " " . $dir);

                system("bsdtar -xf '" . $dir . "." . pathinfo($location, PATHINFO_EXTENSION) . "' -C '" . $dir . "'");
                // exec('unrar x ' . $dir . "." . pathinfo($location, PATHINFO_EXTENSION) . '" ' . $dir);
                dd("bsdtar -xf '" . $dir . "." . pathinfo($location, PATHINFO_EXTENSION) . "' -C '" . $dir . "'");

                // exec("unrar x '" . $dir);
            } else if (pathinfo($location, PATHINFO_EXTENSION) == "7z") {
                mkdir($dir, 0777, true);
                exec("bsdtar -xf '" . $dir . "." . pathinfo($location, PATHINFO_EXTENSION) . "' -C '" . $dir . "'");
            }
        }
        $images = scandir($dir);
        chmod($dir, 0777);
        foreach ($images as $image) {
            if (is_dir($dir . '/' . $image)) {

                chmod($dir . '/' . $image, 0777);
                if ($image != '..' && $image != '.') {
                    $img = scandir($dir . '/' . $image);
                    foreach ($img as $i) {
                        if ($i != '..' && $i != '.') {
                            chmod($dir . '/' . $image . '/' . $i, 0777);
                            rename($dir . '/' . $image . '/' . $i, $dir . '/' . $i);
                        }
                    }
                    rmdir($dir . '/' . $image);
                }
            }
        }
        $images = scandir($dir);


        try {
            rmdir($dir);
        } catch (\Exception $e) {
            // dd($e);
        }
        return view('gallery', ['images' => $images, 'id' => $id, 'dir' => $dir, 'name' => $name, 'location' => $location, 'hide_menu' => $hide_menu]);
    }

    public function photo_destroy(request $request)
    {
        // dd($request);
        $dir = $request->dir;
        $name = $request->name;
        $location = $request->location;
        $image = $request->image;
        unlink($dir . '/' . $image);
        $zip = Zip::open($dir . '.' . pathinfo($location, PATHINFO_EXTENSION));
        $zip->delete($image);
        $zip->close();

        return redirect()->back();
    }

    public function download($id, $location)
    {
        $path = '/storage/files/' . $id . '/' . $location;
        return Storage::download($path);
    }
    public function doc_status_change(request $request)
    {
        $document = Attachment::findOrFail($request->id);
        $document->id_status = $request->doc_status;
        $application = application::findOrFail($document->id_application);
        switch ($document->id_status) {
            case 1:
                $application->id_status = 12;
                break;
            case 10:
                $application->id_status = 16;
                break;
        }
        $application->save();
        $document->save();
        return redirect()->back();
    }
    public function back()
    {
        return redirect()->back();
    }
}
