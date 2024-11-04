<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\DTO\Message;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\DTO\Photo;
use DefStudio\Telegraph\DTO\TelegramUpdate;
use App\Models\User as UserSys;
use App\Models\application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\DB;
use App\Models\city;
use App\Models\present;
use App\Models\telegram_action;
use Jenssegers\Date\Date;
use \ZanySoft\Zip\Facades\Zip;
use \Golchha21\ReSmushIt\Facades\Optimize;
use App\Models\attachment;
use App\Models\User as user_prof;



class Handler extends WebhookHandler
{
    public function messUpdate($user_id, $id)
    {
        $user = user_prof::where('telegram_id', '=', $user_id);
        if ($user->message_id)
            $this->chat->deleteMessage($user->message_id)->send();
        $user->message_id = $id;
        $user->save();
        return 1;
    }
    public function handleChatMessage(Stringable $text): void
    {
        $telegram_action = telegram_action::where('id_chat', '=', $this->chat->chat_id)->first();
        // $this->chat->message("{$telegram_action->action}")->send();
        $path = '';
        if ($telegram_action->action == 1) {

            $path = "storage/files/" . $telegram_action->id_application . '/';
            $name = '';
            $zip_name = '';
            $load = true;
            if ($telegram_action->number < 31) {
                switch ($telegram_action->number) {
                    case 0:
                        $load = false;
                        break;
                    case 21:
                        $path = $path . "Фото_первичного_осмотра";
                        $name = "Фото первичного осмотра";
                        $zip_name = "Фото_первичного_осмотра";
                        break;
                    case 3:
                        $path = $path . "Договор_с_клиентом";
                        $name = "Договор с клиентом";
                        $zip_name = "Договор_с_клиентом";
                        break;
                    case 5:
                        $path = $path . "Доп_соглашение";
                        $name = "Доп соглашение";
                        $zip_name = "Доп_соглашение";
                        break;
                    case 7:
                        $path = $path . "Информационный_акт";
                        $name = "Информационный акт";
                        $zip_name = "Информационный_акт";
                        break;
                    case 9:
                        $path = $path . "Акт_приема_автомобиля_на_ремонт";
                        $name = "Акт приема автомобиля на ремонт";
                        $zip_name = "Акт_приема_автомобиля_на_ремонт";

                        break;
                    case 10:
                        $path = $path . "Фото_приема_автомобиля";
                        $name = "Фото приема автомобиля";
                        $zip_name = "Фото_приема_автомобиля";

                        break;
                    case 12:
                        $path = $path . "Акт_скрытых_повреждений";
                        $name = "Акт скрытых повреждений";
                        $zip_name = "Акт_скрытых_повреждений";
                        break;
                    case 30:
                        $path = $path . "Фото_дефектовки";
                        $name = "Фото дефектовки";
                        $zip_name = "Фото_дефектовки";
                        break;
                    case 14:
                        $path = $path . "Акт_сдачи_автомобиля";
                        $name = "Акт сдачи автомобиля";
                        $zip_name = "Акт_сдачи_автомобиля";

                        break;
                    case 15:
                        $path = $path . "Фото_сдачи_автомобиля";
                        $name = "Фото сдачи автомобиля";
                        $zip_name = "Фото_сдачи_автомобиля";
                        break;
                }
                if ($load) {
                    $path = $path . "_" . date('d-m-y');
                    $zip_name = $zip_name . "_" . date('d-m-y') . ".zip";
                    $name = $name . " " . date('d-m-y');
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $zip_path = $path . '.zip';
                    $zip_bool = true;
                    if (file_exists($zip_path)) {
                        $zip = Zip::open($zip_path);
                        $zip_bool = false;
                    } else {
                        $zip = Zip::create($zip_path);
                    }
                    $filename = "img_" . date('d-m-y_hms') . '(' . $telegram_action->count . ").jpg";
                    $filename = $this->bot->find(1)->store($this->message->photos()->get(2), $path, $filename);
                    $size = getimagesize($filename); // если это изображение и у него определён размер, то продолжаем
                    if ($size) {
                        $telegram_action->count = $telegram_action->count + 1;
                        $keyboard = [];
                        $keyboard[0] = Button::make("ГОТОВО")->action("documents")->param('id', "{$telegram_action->id_application}");

                        $message = $this->chat->message("Получено изображений: {$telegram_action->count}")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons($keyboard)
                            )
                            ->send();
                        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
                        // $file = "storage/files/405/test.txt";
                        // file_put_contents($file, json_decode($message)->result->message_id);
                        // // Optimize::path($filename);
                        $zip->setpath($path)->add($filename);
                        // $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $this->chat->deleteMessage($this->message->id())->send();
                    } else {
                        $this->chat->deleteMessage($this->message->id())->send();
                    }
                    $zip->close();
                    $document = attachment::updateOrCreate(
                        ['id_application' => $telegram_action->id_application, 'file_name' => $name],
                        [
                            'id_application' => $telegram_action->id_application,
                            'id_status' => $telegram_action->number,
                            'file_name' => $name,
                            'message_id' => 0,
                            'mime_type' => 'application/zip-compressed',
                            'file_size' => filesize($zip_path),
                            'location' => $zip_name,
                        ]
                    );
                }
            } else {
                $location = '';
                switch ($telegram_action->number) {
                    case 31:
                        $name = "Паспорт (стр 2-3)";
                        $location = "Паспорт_(стр_2-3)";
                        break;
                    case 32:
                        $name = "СТС (лицевая сторона)";
                        $location = "СТС_(лицевая_сторона)";
                        break;
                    case 33:
                        $name = "СТС (обратная сторона)";
                        $location = "СТС_(обратная_сторона)";
                        break;
                    case 34:
                        $name = "В/У (лицевая сторона)";
                        $location = "В/У_(лицевая_сторона)";
                        break;
                }
                $location = $location . ".jpg";
                $filename = $this->bot->find(1)->store($this->message->photos()->last(), $path, $location);
                $telegram_action->count = $telegram_action->count + 1;
                if ($telegram_action->message_id)
                    $this->chat->deleteMessage($telegram_action->message_id)->send();
                $keyboard = [];
                $keyboard[0] = Button::make("ГОТОВО")->action("documents")->param('id', "{$telegram_action->id_application}");

                $message = $this->chat->message("Получено изображений: {$telegram_action->count} Нажмите ГОТОВО или отправьте изображение заново")
                    ->keyboard(
                        Keyboard::make()
                            ->buttons($keyboard)
                    )
                    ->send();
                $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
                $telegram_action->message_id = json_decode($message)->result->message_id;
                $telegram_action->save();
                $this->chat->deleteMessage($this->message->id())->send();
                $document = attachment::updateOrCreate(
                    ['id_application' => $telegram_action->id_application, 'file_name' => $name],
                    [
                        'id_application' => $telegram_action->id_application,
                        'id_status' => $telegram_action->number,
                        'file_name' => $name,
                        'message_id' => 0,
                        'mime_type' => 'application/zip-compressed',
                        'file_size' => filesize($path . '/' . $location),
                        'location' => $location,
                    ]
                );
            }
        }
    }
    public function hello(string $name): void
    {
        $this->reply("Привет, $name!");
    }

    public function help(): void
    {
        $this->reply('*Привет!* Пока я имею только говорить привет.');
    }

    public function about_me(): void
    {
        $this->chat->message("{$this->chat->chat_id}")->send();
        $this->chat->message('-------------')
            ->replyKeyboard(
                ReplyKeyboard::make()->buttons([
                    ReplyButton::make('Поделиться контактом')->requestContact(),
                ])->resize()
            )->send();
    }

    public function applications(): void
    {
        $user = UserSys::where('telegram_id', '=', $this->chat->chat_id)->first();
        if ($user->GroupId < 5 || $user->GroupId > 6) {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '12');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
                ->get()
                ->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '12');
            })
                ->where('applications.id_MRK', '=', $user->id)
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
                ->get()->sortDesc();
        }
        $i = 0;
        $keyboard = [];

        foreach ($applications as $application) {
            $keyboard[$i++] = Button::make("{$application->id}|  {$application->gnz}")->action('application')->param('id', "{$application->id}");
        }
        $message = $this->chat->message("Список заявок")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
    }
    public function directions(): void
    {
        $user = UserSys::where('telegram_id', '=', $this->chat->chat_id)->first();
        if ($user->GroupId < 5 || $user->GroupId > 6) {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '>', '11')
                    ->where('id_status', '<', '25');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
                ->get();
        } else {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '>', '11')
                    ->where('id_status', '<', '25');
            })
                ->where('applications.id_MRK', '=', $user->id)
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
                ->get();
        }
        $i = 0;
        $keyboard = [];

        foreach ($applications as $application) {
            $keyboard[$i++] = Button::make("{$application->id}|  {$application->gnz}")->action('application')->param('id', "{$application->id}");
        }
        $message = $this->chat->message("Список направлений")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
    }
    public function application(): void
    {
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('users as spms', 'applications.id_spm', '=', 'spms.id')
            ->leftjoin('users as mrks', 'applications.id_mrk', '=', 'mrks.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'services.service_name', 'spms.name as spm', 'mrks.name as mrk')
            ->where('applications.id', '=', $this->data->get('id'))
            ->first();
        $date = Date::parse($application->created_at)->format('j F Yг. H:i');
        $message = $this->chat->message(
            "#|{$application->id}\n<i>Дата заявки</i>:{$date}\n<i>Страховая компания</i>: {$application->name}\n<i>Марка машины</i>: {$application->CarModel}\n<i>ГНЗ</i>: {$application->gnz}\n<i>Статус заявки</i>: {$application->StatusName}\n<i>Клиент</i>: {$application->ClientName}\n<i>Телефон клиента</i>: {$application->ClientPhone}
            "
        )->keyboard(Keyboard::make()
            ->buttons([
                Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$application->id}?hide_menu=1"),
                Button::make("Документы")->action("documents")->param('id', "{$application->id}"),

            ]))
            ->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);
    }
    public function documents(): void
    {
        $telegram_action = telegram_action::updateOrCreate(
            ['id_chat' => $this->chat->chat_id],
            [
                'id_chat' => $this->chat->chat_id,
                'id_application' => $this->data->get('id'),
                'action' => 1,
                'number' => 0,
                'id_message' => 0,
                'count' => 0,
                'message_id' => 0
            ]
        );
        $documents = DB::table('attachments')
            ->where('id_application', '=', $this->data->get('id'))
            ->get()->all();
        $i = 0;
        $keyboard = [];
        $dir = 'storage/files/' . $this->data->get('id');
        $application = application::where('id', '=', $this->data->get('id'))->first();
        foreach ($documents as $attach) {
            if (pathinfo($attach->location, PATHINFO_EXTENSION) != 'gif' && pathinfo($attach->location, PATHINFO_EXTENSION) != 'png') {
                if (
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                    pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
                )
                    $path = 'https://mandarin-portal.ru/gallery/' . $this->data->get('id') . '/' . $attach->location . '/' . $attach->file_name . '?hide_menu=1';
                else

                    $path = "https://mandarin-portal.ru/" . $dir . '/' . $attach->location;

                $name = pathinfo($attach->file_name, PATHINFO_FILENAME);
                $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
            }
        }
        $keyboard[$i++] = Button::make("Основная информация")->action("application")->param('id', "{$this->data->get('id')}");
        $keyboard[$i++] = Button::make("Отправить документ")->action("doc_load")->param('id', "{$this->data->get('id')}");
        $message = $this->chat->message("Документы|Заявка {$application->id}| {$application->gnz}")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )


            ->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
    }
    public function doc_load(): void
    {
        $app = application::where('id', '=', $this->data->get('id'))->first();
        $keyboard = [];
        $i = 0;
        $keyboard[$i++] = Button::make("Назад")->action("documents")->param('id', "{$this->data->get('id')}");
        $keyboard[$i++] = Button::make("Распознать данные")->action("doc_ocr")->param('id', "{$this->data->get('id')}");
        $keyboard[$i++] = Button::make("Паспорт (стр 2-3)")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "31");
        $keyboard[$i++] = Button::make("СТС (лицевая сторона)")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "32");
        $keyboard[$i++] = Button::make("СТС (обратная сторона)")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "33");
        $keyboard[$i++] = Button::make("В/У (лицевая сторона)")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "34");
        $keyboard[$i++] = Button::make("Фото первичного осмотра")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "21");
        if ($app->id_status < 25) {
            $keyboard[$i++] = Button::make("Договор с клиентом")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "3");
            $keyboard[$i++] = Button::make("Доп соглашение")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "5");
            $keyboard[$i++] = Button::make("Информационный акт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "7");
            $keyboard[$i++] = Button::make("Акт приема автомобиля на ремонт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "9");
            $keyboard[$i++] = Button::make("Фото приема автомобиля на ремонт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "10");
            $keyboard[$i++] = Button::make("Акт скрытых повреждений")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "12");
            $keyboard[$i++] = Button::make("Фото дефектовки")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "30");
            $keyboard[$i++] = Button::make("Акт сдачи автомобиля")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "14");
            $keyboard[$i++] = Button::make("Фото сдачи автомобиля")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "15");
        }
        $message = $this->chat->message("Выберите тип загружаемых данных")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )
            ->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
    }

    public function doc_save(): void
    {
        $telegram_action = telegram_action::updateOrCreate(
            ['id_chat' => $this->chat->chat_id],
            [
                'id_chat' => $this->chat->chat_id,
                'id_application' => $this->data->get('id'),
                'action' => 1,
                'number' => $this->data->get('id_doc'),
                'id_message' => 0,
                'count' => 0,
                'message_id' => 0
            ]
        );
        $keyboard = [];
        $keyboard[0] = Button::make("Назад")->action("documents")->param('id', "{$this->data->get('id')}");
        $message = "";

        switch ($this->data->get('id_doc')) {

            case 21:
                $message = "Отправить фото первичного осмотра";
                break;
            case 3:
                $message = "Отправить договор с клиентом";
                break;
            case 5:
                $message = "Отправить доп соглашение";
                break;
            case 7:
                $message = "Отправить информационный акт";
                break;
            case 9:
                $message = "Отправить акт приема автомобиля на ремонт";
                break;
            case 10:
                $message = "Отправить фото приема автомобиля";
                break;
            case 12:
                $message = "Отправить акт скрытых повреждений";
                break;
            case 30:
                $message = "Отправить фото дефектовки";
                break;
            case 14:
                $message = "Отправить акт сдачи автомобиля";
                break;
            case 15:
                $message = "Отправить фото сдачи автомобиля";
                break;
            case 31:
                $message = "Отправить паспорт (стр 2-3)";
                break;
            case 32:
                $message = "Отправить СТС (лицевая сторона)";
                break;
            case 33:
                $message = "Отправить СТС (обратная сторона)";
                break;
            case 34:
                $message = "Отправить В/У (лицевая сторона)";
                break;
        }

        $message = $this->chat->message($message . ". По окончанию загрузки нажмите кнопку ГОТОВО")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )
            ->send();
        $res = $this->messUpdate($this->chat->chat_id, json_decode($message)->result->message_id);
    }
}
