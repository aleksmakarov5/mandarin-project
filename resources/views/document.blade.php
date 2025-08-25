@extends('header_menu')
@section('title')
    Документы
@endsection

@section('main')
                            @if (!isset($hide_menu))
                                                    @php
        $all = true;
        foreach ($document as $doc) {
            if ($doc->id_status != 0) {
                $all = false;
            }
        }
                                                    @endphp

                                                    <form action="/documents/{{ $id }}/doc_add" method="post" enctype="multipart/form-data">
                                                        @csrf <!-- с версии Laravel 5.6 -->
                                                        <table class='table'>
                                                            <tr>
                                                                <td><input type="file" name="userfile[]" multiple onchange="this.form.submit()"></td>




                                                            </tr>
                                                        </table>


                                                    </form>
                                                    <br>

                                                    <div class="row">
                                                        @foreach ($document as $doc)
                                                            @if (pathinfo($doc->location, PATHINFO_EXTENSION) != 'gif')



                                                                                                                                                                <div class="card" style="width: 20rem;">
                                                                                                                                                                    <div class="row g-0">
                                                                                                                                                                        <div class="col-md-4">
                                                                                                                                                                            <a @if (
                    pathinfo($doc->location, PATHINFO_EXTENSION) == 'zip' ||
                    pathinfo($doc->location, PATHINFO_EXTENSION) == 'rar' ||
                    pathinfo($doc->location, PATHINFO_EXTENSION) == '7z'
                ) href='/gallery/{{ $id }}/{{ $doc->location }}/{{ $doc->file_name }}' target="_blank"
                                                                                                                                                                            @else

                                                                                                                                                                                @if (
                        pathinfo($doc->location, PATHINFO_EXTENSION) == 'docx' ||
                        pathinfo($doc->location, PATHINFO_EXTENSION) == 'doc' ||
                        pathinfo($doc->location, PATHINFO_EXTENSION) == 'rtf'
                    )

                                                                                                                                                                                                                                                                                                                                                                            @if (file_exists('storage/files/' . $id . '/' . pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf'))
                                                                                                                                                                                                                                                                                                                                                                            href="/storage/files/{{ $id }}/{{ pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf' }}"
                                                                                                                                                                                                                                                                                                                                                                            @else
                                                                                                                                                                                                                                                                                                                                                                                @php
                            $pathToSave = 'storage/files/' . $id . '/' . $doc->location;
                            $dir = 'storage/files/' . $id;
                            exec('chmod 755 "' . $pathToSave . '"');
                            exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
                                                                                                                                                                                                                                                                                                                                                                                @endphp
                                                                                                                                                                                                                                                                                                                                                                                href="/storage/files/{{ $id }}/{{ pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf' }}"
                                                                                                                                                                                                                                                                                                                                                                            {{-- href="/storage/files/{{ $id }}/{{ $doc->location }}"  --}}
                                                                                                                                                                                                                                                                                                                                                                            @endif
                                                                                                                                                                                @else href="/storage/files/{{ $id }}/{{ $doc->location }}" @endif
                                                                                                                                                                                            target="_blank"
                                                                                                                                                                            @endif>
                                                                                                                                                            <img src="/storage/img/{{ mb_substr($doc->location, mb_strripos($doc->location, '.') + 1, mb_strlen($doc->location) - mb_strripos($doc->location, '.')) }}.png"
                                                                                                                                                                class="card-img-top"
                                                                                                                                                                alt="{{ mb_substr($doc->mime_type, mb_strpos($doc->mime_type, '/'), strlen($doc->mime_type) - mb_strpos($doc->mime_type, '/')) }}">

                                                                                                                                                            </a>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                        <a href="/document/{{ $id }}/doc_edit/{{ $doc->id }}"><img src="/storage/img/edit.png"
                                                                                                                                                                width="35" alt="edit">
                                                                                                                                                        </a>
                                                                                                                                                        <a onclick="if (!confirm('Точно удалить документ <{{ $doc->file_name }}?>')) return false"
                                                                                                                                                            href="/document/{{ $id }}/doc_delete/{{ $doc->id }}"><img src="/storage/img/delete.png"
                                                                                                                                                                width="35" alt="delete">
                                                                                                                                                        </a>
                                                                                                                                                        <a href="/storage/files/{{ $id }}/{{ $doc->location }}" download><img
                                                                                                                                                                src="/storage/img/download.png" width="35" alt="Скачать">
                                                                                                                                                        </a>
                                                                                                                                                        <div class="card-body">
                                                                                                                                                            @if (isset($document_edit->id))
                                                                                                                                                                @if ($document_edit->id == $doc->id)
                                                                                                                                                                    <form action="/document/{{ $id }}/doc_update/{{ $doc->id }}" method="POST">
                                                                                                                                                                        @csrf
                                                                                                                                                                        <input type="text" name="file_name" value="{{ $doc_name }}">
                                                                                                                                                                        <input type="hidden" name="file_ext" value="{{ $doc_ext }}">
                                                                                                                                                                        <input type="submit" value="ок">
                                                                                                                                                                    </form>
                                                                                                                                                                @else
                                                                                                                                                                    <h5 class="card-title">{{ $doc->file_name == 'image001.png' ? 'Номер телефона' : $doc->file_name }}
                                                                                                                                                                    </h5>
                                                                                                                                                                @endif
                                                                                                                                                            @else
                                                                                                                                                                <h5 class="card-title">{{ $doc->file_name == 'image001.png' ? 'Номер телефона' : $doc->file_name }}
                                                                                                                                                                </h5>
                                                                                                                                                            @endif
                                                                                                                                                            <p class="file-size">
                                                                                                                                                                {{ round($doc->file_size / 1024 / 1024, 2) }} мб</p>


                                                                                                                                                        </div>

                                                                                                                                                    </div>
                                                                                                                                                    </div>

                                                                                                                                                    </div>
                                                            @endif
                                                        @endforeach
                                                </div>
                                                <br>
                            @endif



                            <table class='table table-bordered table-hover  table-striped'>
                                <thead>
                                    <tr>
                                        <th scope="col">Тип документа</th>
                                        <th scope="col">Печатная форма</th>
                                        <th scope="col">Скан документа</th><th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @can('company')
                                                                                                                                            <tr>
                                                                                                                                                <th scope="col"><a title="Отправить на телефон шаблон приглашения клиента на ремонт. Для корректного формирования сообщения необходимо заполнить вкладку Основная информация (ФИО, пол)">СМС приглашение на ремонт</a></th>

                                                                                                                                                <th scope="col" colspan=4>
                                                                                                                                                    <a href="/application/{{ $id }}/sms_send" class="msg_but" title="Отправить ссылку в Telegram"><img
                                                                                                                                                            src="/storage/img/telegram.png" width="30" alt="Добавить"></a>
                                                                                                                                                </th>
                                                                                                                                                <th></th>
                                                                                                                                            </tr>

                                                                                                                                                                                                    @if (isset($application->id_insuranceCompany))


                                                                                                                                                                                                                                                                            @if (($application->id_insuranceCompany == 3 || $application->id_insuranceCompany == 6) && $application->id_status == 28)

                                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                                    <th scope="col">Заявление о страховом возмещении</th>
                                                                                                                                                                                                                                                                                    <th scope="col">
                                                                                                                                                                                                                                                                                        @include('document.form_print', [
                                                    'status_id' => 11,
                                                    'template' => 'export_zayav',
                                                ])
                                                                                                                                                                                                                                                                                    </th>
                                                                                                                                                                                                                                                                                    <th scope="col">
                                                                                                                                                                                                                                                                                        @include('document.form_scan', [
                                                    'status_id' => 12,
                                                    'load_name' => 'Заявление о страховом возмещении',
                                                    'load_location' => 'Заявление_о_страховом_возмещении',
                                                ])
                                                                                                                                                                                                                                                                                    </th>
                                                                                                                                                                                                                                                                                    <th></th>
                                                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                                            @endif
                                                                                                                                                                                                    @endif
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Акт первичного осмотра</th>

                                                                                                                                                                                                        <th scope="col" colspan=2>
                                                                                                                                                                                                            @include('document.form_scan', ['status_id' => 20])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th></th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Фото первичного осмотра автомобиля</th>

                                                                                                                                                                                                        <th scope="col" colspan="2">
                                                                                                                                                                                                            @include('document.form_scan', [
                                            'status_id' => 21,
                                            'load_name' => 'Фото первичного осмотра автомобиля',
                                            'load_location' => 'Фото_первичного_осмотра_автомобиля',
                                        ])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th></th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Смета</th>

                                                                                                                                                                                                        <th scope="col" colspan=2>
                                                                                                                                                                                                            @include('document.form_scan', ['status_id' => 22])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th></th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Направление</th>


                                                                                                                                                                                                        <th scope="col" colspan=2>
                                                                                                                                                                                                            @include('document.form_scan', ['status_id' => 1])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th></th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Калькуляция</th>

                                                                                                                                                                                                        <th scope="col" colspan=2>
                                                                                                                                                                                                            @include('document.form_scan', ['status_id' => '23'])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th></th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                     @if (isset($application->id_insuranceCompany))
                                                                                                                                                                                                        @if ($application->id_insuranceCompany == 1)
                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <th scope="col">Информационный акт</th>
                                                                                                                                                                                                                <th scope="col">
                                                                                                                                                                                                                    @include('document.form_print', ['status_id' => 6, 'template' => 'export_if'])
                                                                                                                                                                                                                </th>
                                                                                                                                                                                                                <th scope="col">
                                                                                                                                                                                                                    @include('document.form_scan', [
                                                    'status_id' => 7,
                                                    'load_name' => 'Информационный акт',
                                                    'load_location' => 'Информационный_акт',
                                                ])
                                                                                                                                                                                                                </th>
                                                                                                                                                                                                                <th></th>
                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                        @endif
                                                                                                                                                                                                    @endif
                                                                                                                                                                                                    @if (isset($application->id_insuranceCompany))
                                                                                                                                                                                                        @if ($application->id_insuranceCompany == 6)


                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <th scope="col">СОГЛАШЕНИЕ О РЕМОНТЕ </th>
                                                                                                                                                                                                                <th scope="col">
                                                                                                                                                                                                                    @include('document.form_print', [
                                                    'status_id' => 39,
                                                    'template' => 'export_sogl',
                                                ])
                                                                                                                                                                                                                </th>
                                                                                                                                                                                                                <th scope="col">
                                                                                                                                                                                                                    @include('document.form_scan', [
                                                    'status_id' => 7,
                                                    'load_name' => 'СОГЛАШЕНИЕ О РЕМОНТЕ',
                                                    'load_location' => 'СОГЛАШЕНИЕ_О_РЕМОНТЕ',
                                                ])
                                                                                                                                                                                                                </th>
                                                                                                                                                                                                                <th></th>
                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                        @endif
                                                                                                                                                                                                    @endif

                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th scope="col">Предварительный заказ наряд
                                                                                                                                                                                                        </th>
                                                                                                                                                                                                        <th scope="col">
                                                                                                                                                                                                            @include('document.form_print', ['status_id' => 16, 'template' => 'export_pzn'])
                                                                                                                                                                                                        </th>

                                                                                                                                                                                                        <th scope="col">
                                                                                                                                                                                                            @include('document.form_scan', [
                                            'status_id' => 17,
                                            'load_name' => 'Заказ наряд',
                                            'load_location' => 'Заказ_наряд',
                                        ])
                                                                                                                                                                                                        </th>
                                                                                                                                                                                         <td>
                                                                                                                                                                                            <div>
                                                                                                                                                                                                @php
                                        $to = [];
                                        $subj = $application->numberInsuranceCompanyApplication . '_ПЗН';
                                        if (isset($application->id_insuranceCompany)) {
                                            if ($application->id_insuranceCompany == 3) {

                                                $to[0] = "drs-sto@ingos.ru";
                                                $to[1] = "sergei.vtorov@ingos.ru";
                                                $to[2] = "sergey.sivkov@ingos.ru";
                                                $to[3] = "andrey.gretchukhin@ingos.ru";
                                            } elseif ($application->id_insuranceCompany == 1) {
                                                $to[0] = "smeta_szfo@vsk.ru";
                                            } elseif ($application->id_insuranceCompany == 5) {
                                                $to[0] = "akt.rem4@sogaz.ru";
                                                $to[1] = "kuznetsov.vitaly@sogaz.ru";
                                                $to[2] = "malygin.leonid@sogaz.ru";
                                            } elseif ($application->id_insuranceCompany == 4) {
                                                $to[0] = "arhangelsk_oup@email.reso.ru";
                                            } elseif ($application->id_insuranceCompany == 7) {
                                                $to[0] = "stoarcuu@ugsk.ru";
                                                $to[1] = "petrunyasv@ugsk.ru";
                                                $to[2] = "menshakoves@ugsk.ru";
                                                $subj .= ' ' . $application->CarModel . ' ' . $application->gnz;

                                            } else {
                                                $to[0] = isset($to_email) ? $to_email : '';
                                            }
                                        } else {
                                            $to[0] = isset($to_email) ? $to_email : '';
                                        }
                                                                                                                                                                                                @endphp
                                                                                                                                                                                        <special-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
                                                                                                                                                                                                        :subject="{{json_encode($subj)}}"
                                                                                                                                                                                                        :attach="{{json_encode(isset($attach) ? $attach : [])}}" :app_id="{{$id}}"
                                                                                                                                                                                        :text="{{json_encode("Направляем предварительный заказ-наряд по убытку №" . $application->numberInsuranceCompanyApplication)}}"
                                                                                                                                                                                                        :sign="{{json_encode(isset($sign) ? $sign : '')}}"
                                                                                                                                                                                                        :status="22" />
                                                                                                                                                                                                        </div>
                                                                                                                                                                                         </td><td>
                                                                                                                                                                                                        <div>
                                                                                                                                                                                                            @php
                                        $to = [];
                                        $to[0] = "alekseiexpert29@yandex.ru";
                                        $to[1] = "pdimon55@mail.ru";
                                                                                                                                                                                                            @endphp
                                                                                                                                                                                                            <special-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
                                                                                                                                                                                                        :subject="{{json_encode('ПЗН ' . $application->CarModel . ' ' . $application->gnz . ' ' . ($application->ub_type ? "Каско" : "Осаго") . ' zn-' . $application->id . '!')}}"
                                                                                                                                                                                                        :attach="{{json_encode(isset($attach) ? $attach : [])}}" :app_id="{{$id}}"
                                                                                                                                                                                        :text="{{json_encode("Добрый день!<br><br>

                                                                                                                                                Просьба составить ПЗН.<br><br>  <i>Тему письма не изменять </i>")}}"
                                                                                                                                                                                                        :sign="{{json_encode(isset($sign) ? $sign : '')}}"
                                                                                                                                                                                                        :status="22" />
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                    @endcan
                                    <tr>
                                        <th scope="col">Договор с клиентом</th>
                                        <th scope="col">
                                            @include('document.form_print', ['status_id' => 2, 'template' => 'export_dk'])
                                        </th>
                                        <th scope="col">
                                            @include('document.form_scan', [
        'status_id' => 3,
        'load_name' => 'Договор с клиентом',
        'load_location' => 'Договор_с_клиентом',
    ])
                                        </th>

                                    </tr>
                                    <tr>
                                        <th scope="col">Дополнительное соглашение</th>
                                        <th scope="col">
                                            @include('document.form_print', ['status_id' => 4, 'template' => 'export_ds'])
                                        </th>
                                        <th scope="col">
                                            @include('document.form_scan', [
        'status_id' => 5,
        'load_name' => 'Дополнительное соглашение с клиентом',
        'load_location' => 'Дополнительное_соглашение_с_клиентом',
    ])
                                        </th>
                                    </tr>

                                    <tr>
                                        <th scope="col">Акт приема автомобиля на ремонт</th>
                                        <th scope="col">
                                            @include('document.form_print', ['status_id' => 8, 'template' => 'export_ap'])
                                        </th>
                                        <th scope="col">
                                            @include('document.form_scan', [
        'status_id' => 9,
        'load_name' => 'Акт приема автомобиля',
        'load_location' => 'Акт_приема_автомобиля',
    ])
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Фото приема автомобиля на ремонт</th>


                                        <th scope="col" colspan=2>
                                            @include('document.form_scan', [
        'status_id' => 10,
        'load_name' => 'Фото приема автомобиля',
        'load_location' => 'Фото_приема_автомобиля',
    ])
                                        </th>
                                    </tr>
                                    @if (!$def)
                                                        <tr>
                                                            <th scope="col">Акт скрытых повреждений</th>
                                                            <th scope="col">
                                                                @include('document.form_print', [
            'status_id' => 11,
            'template' => 'export_hda',
        ])
                                                            </th>
                                                            <th scope="col">
                                                                @include('document.form_scan', [
            'status_id' => 12,
            'load_name' => 'Акт скрытых повреждений',
            'load_location' => 'Акт_скрытых_повреждений',
        ])
                                                            </th>
                                                            <td>
                                            <div>
                                                @php
        $to = [];
        if (isset($application->id_insuranceCompany)) {
            if ($application->id_insuranceCompany == 3) {
                $to[0] = "drs-soglas@ingos.ru";
                $to[1] = "sergei.vtorov@ingos.ru";
            } elseif ($application->id_insuranceCompany == 1) {
                $to[0] = "smeta_szfo@vsk.ru";
            } elseif ($application->id_insuranceCompany == 5) {
                $to[0] = "akt.rem4@sogaz.ru";
                $to[1] = "kuznetsov.vitaly@sogaz.ru";
                $to[2] = "malygin.leonid@sogaz.ru";
            } elseif ($application->id_insuranceCompany == 4) {
                $to[0] = "arhangelsk_oup@email.reso.ru";
            } elseif ($application->id_insuranceCompany == 7) {
                $to[0] = "stoarcuu@ugsk.ru";
            } else {
                $to[0] = isset($to_email) ? $to_email : '';
            }
        } else {
            $to[0] = isset($to_email) ? $to_email : '';
        }
                                                @endphp
                                        <special-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
                                                        :subject="{{json_encode($application->numberInsuranceCompanyApplication . '_АктСП')}}"
                                                        :attach="{{json_encode(isset($attach) ? $attach : [])}}" :app_id="{{$id}}"
                                        :text="{{json_encode("Направляем акт скрытых повреждений и фото дефектовки по убытку №" . $application->numberInsuranceCompanyApplication)}}"
                                                        :sign="{{json_encode(isset($sign) ? $sign : '')}}"
                                                        :status="22" />
                                                        </div>
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="col">Фото деффектовки</th>


                                                            <th scope="col" colspan=2>
                                                                @include('document.form_scan', [
            'status_id' => 10,
            'load_name' => 'Фото деффектовки',
            'load_location' => 'Фото_деффектовки',
        ])
                                                            </th>
                                                        </tr>
                                    @endif
                                    <tr>
                                        <th scope="col">Акт сдачи автомобиля</th>
                                        <th scope="col">
                                            @include('document.form_print', ['status_id' => 13, 'template' => 'export_as'])
                                        </th>
                                        <th scope="col">
                                            @include('document.form_scan', [
        'status_id' => 14,
        'load_name' => 'Акт сдачи автомобиля',
        'load_location' => 'Акт_сдачи_автомобиля',
    ])
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Фото сдачи автомобиля</th>


                                        <th scope="col" colspan=2>
                                            @include('document.form_scan', [
        'status_id' => 15,
        'load_name' => 'Фото cдачи автомобиля',
        'load_location' => 'Фото_cдачи_автомобиля',
    ])
                                        </th>
                                    </tr>
                                    @can('company')
                                                                {{-- <tr>
                                                                    <th scope="col">Заказ наряд</th>
                                                                    <th scope="col">
                                                                        @include('document.form_print', ['status_id' => 16, 'template' => 'export_zn'])
                                                                    </th>
                                                                    <th scope="col">
                                                                        @include('document.form_scan', [
                                                'status_id' => 17,
                                                'load_name' => 'Заказ наряд',
                                                'load_location' => 'Заказ_наряд',
                                            ])
                                                                    </th>
                                                                </tr> --}}
                                                                <tr>
                                                                    <th scope="col">Счет</th>
                                                                    <th scope="col">
                                                                        @include('document.form_print', ['status_id' => 18, 'template' => 'export_sch'])
                                                                    </th>
                                                                    <th scope="col">
                                                                        @include('document.form_scan', [
            'status_id' => 19,
            'load_name' => 'Счет',
            'load_location' => 'Счет',
        ])
                                                                    </th>
                                                                    <td>
                                            <div>
                                                @php
        $to = [];
        if (isset($application->id_insuranceCompany)) {
            if ($application->id_insuranceCompany == 3) {
                $to[0] = "drs-sto@ingos.ru";
                $to[1] = "sergei.vtorov@ingos.ru";
            } elseif ($application->id_insuranceCompany == 1) {
                $to[0] = "pay.arhangelsk@vsk.ru";
            } elseif ($application->id_insuranceCompany == 5) {
                $to[0] = "akt.rem4@sogaz.ru";
                $to[1] = "kuznetsov.vitaly@sogaz.ru";
                $to[2] = "malygin.leonid@sogaz.ru";
            } elseif ($application->id_insuranceCompany == 4) {
                $to[0] = "arhangelsk_oup@email.reso.ru";
            } elseif ($application->id_insuranceCompany == 7) {
                $to[0] = "stoarcuu@ugsk.ru";
            } else {
                $to[0] = isset($to_email) ? $to_email : '';
            }
        } else {
            $to[0] = isset($to_email) ? $to_email : '';
        }

                                                @endphp
                                        <special-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
                                                        :subject="{{json_encode($application->numberInsuranceCompanyApplication . '_Счет')}}"
                                                        :attach="{{json_encode(isset($attach) ? $attach : [])}}" :app_id="{{$id}}"
                                        :text="{{json_encode("Направляем комплект документов по факту выполненных работ по убытку №" . $application->numberInsuranceCompanyApplication)}}"
                                                        :sign="{{json_encode(isset($sign) ? $sign : '')}}"
                                                        :status="22" />
                                                        </div>
                                                        </td>
                                                                </tr>
                                    @endcan
                                            </tbody>
                                        </table>

@endsection
