<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\application;
use App\Models\user_filter;
use App\Models\attachment;
use ZanySoft\Zip\Facades\Zip;

class FileController extends Controller
{
    public function file_upload(Request $request)
    {

        $data = $this->validate($request, [
            'id_app' => 'required',
            'load_name' => 'required',
            'load_location' => 'required',
        ]);
        $id = $data['id_app'];
        // return $data;


        $document = new Attachment();
        if ($request->file('file')) {

            $dir = '/storage/files/' . $id . '/' . $request->load_location . "_" . date('d-m-y');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $zip_path = 'storage/files/' . $id . '/' . $data['load_location'] . "_" . date('d-m-y') . '.zip';
            $zip_bool = true;

            if (file_exists($zip_path)) {
                $zip = Zip::open($zip_path);
                $zip_bool = false;
            } else {
                $zip = Zip::create($zip_path);
            }

            $file = $request->file('file');

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
                $fileNameToStore = $filename . "." . $extention;
                // Сохраняем файл
                $path = $file->storeAs('public/files/' . $upload_folder, $fileNameToStore);

                $filename = 'storage/files/' . $upload_folder . '/' . $fileNameToStore;

                // ПРОВЕРКА НА ИЗОБРАЖЕНИЕ

                // $size = getimagesize($filename); // если это изображение и у него определён размер, то продолжаем
                // if ($size) {
                //     $width = 800;
                //     $height = 800;

                //     // РАБОТАЕМ КОРРЕКТНО. ОПРЕДЕЛЯЕМ ТИП
                //     header("Content-type: {$size['mime']}");
                //     try {
                //         $exif = exif_read_data($filename);
                //     } catch (Exception $e) {
                //         $exif = null;
                //     }
                //     // file_put_contents('data.txt', json_encode($exif['Orientation']));

                //     // ПОЛУЧАЕМ НОВЫЕ РАЗМЕРЫ

                //     list($width_orig, $height_orig) = getimagesize($filename);

                //     if ($width && ($width_orig < $height_orig)) {

                //         $width = ($height / $height_orig) * $width_orig;
                //     } else {
                //         $height = ($width / $width_orig) * $height_orig;
                //     }

                //     // ПЕРЕСОХРАНЯЕМ ИЗОБРАЖЕНИЕ
                //     $image_p = imagecreatetruecolor($width, $height);
                //     $image = imagecreatefromjpeg($filename);
                //     imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                //     // СОЗДАЁМ
                //     $img_name = 'storage/files/' . $upload_folder . '/' . pathinfo($filenameWithExt, PATHINFO_FILENAME) . 'compressed.' . pathinfo($filenameWithExt, PATHINFO_EXTENSION);
                //     imagejpeg($image_p, $img_name, 60);

                //     // УДАЛЯЕМ ИСХОДНИК - НУЖЕН АДРЕС. НАПРИМЕР, images/img001.jpg
                //     unlink($filename);
                //     $filename = $img_name;
                //     if (isset($exif['Orientation'])) {
                //         $degrees = 0;
                //         if ($exif['Orientation'] == 6) {
                //             $degrees = 270;
                //         }
                //         if ($exif['Orientation'] == 3) {
                //             $degrees = 180;
                //         }
                //         if ($exif['Orientation'] == 8) {
                //             $degrees = 90;
                //         }
                //         // Загрузка изображения
                //         $source = imagecreatefromjpeg($filename);
                //         // Поворот
                //         $rotate = imagerotate($source, $degrees, 0);
                //         // Вывод
                //         imagejpeg($rotate, $img_name, 100);
                //         // Высвобождение памяти
                //         imagedestroy($source);
                //         imagedestroy($rotate);
                //     }
                //     $zip->setpath($dir)->add($fileNameToStore);
                //     // $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // }
            }


            $zip->close();
            if ($zip_bool) {
                $zip_path = '/storage/files/' . $id . '/' . $request->load_location . "_" . date('d-m-y') . '.zip';
                $document->file_name = $request->load_name . "_" . date('d-m-y');
                $document->message_id = 0;
                $document->mime_type = 'application/zip-compressed';
                $document->id_application = $id;

                $document->file_size = filesize($zip_path);

                $document->id_status = $request->status_id;
                $document->location = $request->load_location . "_" . date('d-m-y') . '.zip';
                // dd($document);
                $document->save();
            }



            // $file->move(public_path('uploads'), $file->getClientOriginalName());
            return $file->getClientOriginalName();
        } else {
            return false;
        }
    }
}
