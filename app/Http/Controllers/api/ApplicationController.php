<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\application;
use App\Models\user_filter;
use App\Models\attachment;
use \ZanySoft\Zip\Facades\Zip;
use \Golchha21\ReSmushIt\Facades\Optimize;

class ApplicationController extends Controller
{
    public function destroy($id)
    {
        $application = application::findOrFail($id);
        $application->delete();
        return true;
    }
    public function delete_filter(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required',
            'id_user' => 'required'
        ]);

        $user_filter = user_filter::where('id_user', '=', $data['id_user'])->where('id_city', '=', $data['id_city'])->first();
        if ($user_filter)  // if filter exists then delete it
            $user_filter->delete();

        return true;
    }
    public function add_filter(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required',
            'id_user' => 'required'
        ]);
        $user_filter = user_filter::firstOrCreate(['id_user' => $data['id_user'], 'id_city' => $data['id_city']]);

        return true;
    }
    public function delete_filters(Request $request)
    {
        $data = $this->validate($request, [

            'id_user' => 'required'
        ]);
        $user_filters = user_filter::where('id_user', '=', $data['id_user'])->get();
        foreach ($user_filters as $user_filter) {
            $user_filter->delete();
        }
        return true;
    }
    public function add_filters(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required|array',
            'id_user' => 'required'
        ]);
        foreach ($data['id_city'] as $city_id) {
            $user_filter = user_filter::firstOrCreate(['id_user' => $data['id_user'], 'id_city' => $city_id]);
        }

        return true;
    }
    public function file_upload(Request $request)
    {

        // sleep(0.5);
        $data = $this->validate($request, [
            'id_app' => 'required',
            'load_name' => 'required',
            'load_location' => 'required',
        ]);
        $id = $data['id_app'];
        $exif = 0;
        // return $data;
        if ($request->File('file')) {

            $dir = 'storage/files/' . $id . '/' . $request->load_location . "_" . date('d-m-y');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $zip_path = 'storage/files/' . $id . '/' . $request->load_location . "_" . date('d-m-y') . '.zip';

            $file = $request->file('file');
            if ($file->getClientOriginalExtension() != 'png' && $file->getClientOriginalExtension() != 'webp') {
                if ($file->getClientOriginalExtension() == 'pdf') {
                    $zip_bool = false;
                    // dd($file);
                    $upload_folder = '' . $id;
                    $filenameWithExt = $file->getClientOriginalName(); //
                    // Имя и расширение файла
                    // Только оригинальное имя файла
                    $filename = $request->load_name . "_" . date('d-m-y_hms');
                    // Расширение
                    $extention = $file->getClientOriginalExtension();
                    // Путь для сохранения
                    $fileNameToStore = $filename . '.' . $extention;
                    // Сохраняем файл
                    $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                    $filename = 'storage/files/' . $id . '/' . $fileNameToStore;
                    // dd($file);

                    $document = new Attachment();
                    $filename = $request->load_name . "_" . date('d-m-y');
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
                    $upload_folder = '' . $id . '/' . $request->load_location . "_" . date('d-m-y');
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
                        // Optimize::path($filename);
                        $document = new Attachment();
                        $width = 800;
                        $height = 800;

                        // РАБОТАЕМ КОРРЕКТНО. ОПРЕДЕЛЯЕМ ТИП
                        header("Content-type: {$size['mime']}");
                        $exif = exif_read_data($filename);
                        // file_put_contents('data.txt', json_encode($exif['Orientation']));

                        // ПОЛУЧАЕМ НОВЫЕ РАЗМЕРЫ
                        list($width_orig, $height_orig) = getimagesize($filename);

                        if ($width && ($width_orig < $height_orig)) {

                            $width = ($height / $height_orig) * $width_orig;
                        } else {
                            $height = ($width / $width_orig) * $height_orig;
                        }

                        // ПЕРЕСОХРАНЯЕМ ИЗОБРАЖЕНИЕ
                        $image_p = imagecreatetruecolor($width, $height);
                        $image = imagecreatefromjpeg($filename);
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                        // СОЗДАЁМ
                        $img_name = $dir . '/' . pathinfo($filenameWithExt, PATHINFO_FILENAME) . 'compressed.' . pathinfo($filenameWithExt, PATHINFO_EXTENSION);
                        imagejpeg($image_p, $img_name, 60);

                        // УДАЛЯЕМ ИСХОДНИК - НУЖЕН АДРЕС. НАПРИМЕР, images/img001.jpg
                        unlink($filename);
                        $filename = $img_name;
                        if (isset($exif['Orientation'])) {
                            $degrees = 0;
                            if ($exif['Orientation'] == 6) {
                                $degrees = 270;
                            }
                            if ($exif['Orientation'] == 3) {
                                $degrees = 180;
                            }
                            if ($exif['Orientation'] == 8) {
                                $degrees = 90;
                            }
                            // Загрузка изображения
                            $source = imagecreatefromjpeg($filename);
                            // Поворот
                            $rotate = imagerotate($source, $degrees, 0);
                            // Вывод
                            imagejpeg($rotate, $img_name, 100);
                            // Высвобождение памяти
                            imagedestroy($source);
                            imagedestroy($rotate);
                        }
                        $zip_bool = true;
                        if (file_exists($zip_path)) {
                            while (!$zip = Zip::open($zip_path)) {
                                sleep(0.5);
                            }

                            $zip_bool = false;
                        } else {
                            $zip = Zip::create($zip_path);
                        }
                        $res = $zip->add($filename);
                        // $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $zip->close();
                        attachment::updateOrCreate([
                            'id_application' => $id,
                            'location' => $request->load_location . "_" . date('d-m-y') . '.zip',
                            'id_status' => $request->status_id,
                            'message_id' => 0,
                            'file_name' => $request->load_name . "_" . date('d-m-y'),
                            'mime_type' => 'application/zip-compressed',
                        ], ['file_size' => filesize($zip_path)]);
                    }
                }
            }




            // if ($zip_bool) {
            //     $document->file_name = $request->load_name . "_" . date('d-m-y');
            //     $document->message_id = 0;
            //     $document->mime_type = 'application/zip-compressed';
            //     $document->id_application = $id;

            //     $document->id_status = $request->status_id;
            //     $document->location = $request->load_location . "_" . date('d-m-y') . '.zip';
            //     // dd($document);
            //     try {
            //         $document->file_size = filesize($zip_path);
            //     } catch (Exception $e) {
            //     }
            //     $document->save();
            // }


            $num = $request->num;
            // $file->move(public_path('uploads'), $file->getClientOriginalName());
            return [$num, isset($exif['Orientation']) ? $exif['Orientation'] : 0];
        } else {
            return false;
        }
    }
}