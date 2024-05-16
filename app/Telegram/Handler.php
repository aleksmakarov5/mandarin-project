<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\DTO\User;
use App\Models\User as UserSys;
use App\Models\application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\DB;
use App\Models\city;
use App\Models\present;
use App\Models\promotion;
use Jenssegers\Date\Date;

class Handler extends WebhookHandler
{
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
                    ->where('id_status', '<', '6');
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
                    ->where('id_status', '<', '6');
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
                    ->where('id_status', '>', '7')
                    ->where('id_status', '<', '25');
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
                    ->where('id_status', '>', '7')
                    ->where('id_status', '<', '25');
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
        $keyboard[$i++] = Button::make("Основная информация")->action("application")->param('id', "{$this->data->get('id')}");
        $keyboard[$i++] = Button::make("Загрузить документ")->webApp("https://mandarin-portal.ru/document/{$this->data->get('id')}?hide_menu=1");
        $this->chat->message("Документы")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )
            ->send();
    }
}
