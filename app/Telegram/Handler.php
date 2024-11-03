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



class Handler extends WebhookHandler
{
    public function handleChatMessage(Stringable $text): void
    {
        $telegram_action = telegram_action::where('id_chat', '=', $this->chat->chat_id)->first();
        // $this->chat->message("{$telegram_action->action}")->send();
        $path = '';
        if ($telegram_action->action == 1) {
            $path = "storage/files/" . $telegram_action->id_application . '/';
            switch ($telegram_action->number) {
                case 21:
                    $path = $path . "Фото_первичного_осмотра";
                    break;
                case 3:
                    $path = $path . "Договор_с_клиентом";
                    break;
                case 5:
                    $path = $path . "Доп_соглашение";
                    break;
                case 7:
                    $path = $path . "Информационный_акт";
                    break;
                case 9:
                    $path = $path . "Акт_приема_автомобиля_на_ремонт";
                    break;
                case 10:
                    $path = $path . "Фото_приема_автомобиля";
                    break;
                case 12:
                    $path = $path . "Акт_скрытых_повреждений";
                    break;
                case 30:
                    $path = $path . "Фото_дефектовки";
                    break;
                case 14:
                    $path = $path . "Акт_сдачи_автомобиля";
                    break;
                case 15:
                    $path = $path . "Фото_сдачи_автомобиля";
                    break;
            }
            $path = $path . "_" . date('d-m-y');
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
            $file = $path . ".txt";
            $filename = "img_" . date('d-m-y_hms') . ".jpg";
            $file = $this->bot->find(1)->store($this->message->photos()->last(), $path, $filename);
            // $file = $this->chat->message("{$filename}")->send();
            $size = getimagesize($filename); // если это изображение и у него определён размер, то продолжаем
            if ($size) {
                Optimize::path($file);
                $zip->setpath($path)->add($filename);
                // $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            }
            $zip->close();
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
        $this->chat->message("Список заявок")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )->send();
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
        $this->chat->message("Список направлений")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )->send();
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
        $this->chat->message(
            "#|{$application->id}\n<i>Дата заявки</i>:{$date}\n<i>Страховая компания</i>: {$application->name}\n<i>Марка машины</i>: {$application->CarModel}\n<i>ГНЗ</i>: {$application->gnz}\n<i>Статус заявки</i>: {$application->StatusName}\n<i>Клиент</i>: {$application->ClientName}\n<i>Телефон клиента</i>: {$application->ClientPhone}
            "
        )->keyboard(Keyboard::make()
            ->buttons([
                Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$application->id}?hide_menu=1"),
                Button::make("Документы")->action("documents")->param('id', "{$application->id}"),

            ]))
            ->send();
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);
    }
    public function documents(): void
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $this->data->get('id'))
            ->get()->all();
        $i = 0;
        $keyboard = [];
        $dir = 'storage/files/' . $this->data->get('id');

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
        $this->chat->message("Документы")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )


            ->send();
    }
    public function doc_load(): void
    {
        $keyboard = [];
        $i = 0;
        $keyboard[$i++] = Button::make("Назад")->action("documents")->param('id', "{$this->data->get('id')}");
        $keyboard[$i++] = Button::make("Фото первичного осмотра")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "21");
        $keyboard[$i++] = Button::make("Договор с клиентом")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "3");
        $keyboard[$i++] = Button::make("Доп соглашение")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "5");
        $keyboard[$i++] = Button::make("Информационный акт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "7");
        $keyboard[$i++] = Button::make("Акт приема автомобиля на ремонт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "9");
        $keyboard[$i++] = Button::make("Фото приема автомобиля на ремонт")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "10");
        $keyboard[$i++] = Button::make("Акт скрытых повреждений")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "12");
        $keyboard[$i++] = Button::make("Фото дефектовки")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "30");
        $keyboard[$i++] = Button::make("Акт сдачи автомобиля")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "14");
        $keyboard[$i++] = Button::make("Фото сдачи автомобиля")->action("doc_save")->param('id', "{$this->data->get('id')}")->param('id_doc', "15");

        $this->chat->message("Выберите тип загружаемых данных")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )
            ->send();
    }

    public function doc_save(): void
    {
        $keyboard = [];
        $telegram_action = telegram_action::updateOrCreate(
            ['id_chat' => $this->chat->chat_id],
            [
                'id_chat' => $this->chat->chat_id,
                'id_application' => $this->data->get('id'),
                'action' => 1,
                'number' => $this->data->get('id_doc'),
                'id_message' => 0
            ]
        );
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
        }

        $this->chat->message($message . ". По окончанию загрузки нажмите кнопку ГОТОВО")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )
            ->send();
    }
}
