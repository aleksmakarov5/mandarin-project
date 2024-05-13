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
            $keyboard[$i++] = Button::make("{$application->id}|{$application->gnz}")->action('application')->param('id', "{$application->id}");
        }
        $this->chat->message("Список заявок")
            ->keyboard(
                Keyboard::make()
                    ->buttons($keyboard)
            )->send();
    }
    public function application(): void
    {
        $application = application::find($this->data->get('id'));
        $this->chat->message(
            "
            #|{$application->id}\n
            <i>Дата заявки</i>:
            {$application->created_at}\n
            "
        )->keyboard(Keyboard::make()
            ->buttons([
                Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$application->id}"),
                Button::make("К списку заявок")->action("applications"),
            ]))
            ->send();
    }
}
