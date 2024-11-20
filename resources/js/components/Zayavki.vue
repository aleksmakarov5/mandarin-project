
<template>
<div>
<table id="applications" class="editable">
            <thead>

                <tr>
                    <th scope="col">Дог</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Тайминг</th>
                    <th scope="col">#</th>

                    <th scope="col">МРК</th>
                    <th scope="col">Город</th>
                    <th scope="col">СК</th>
                    <th scope="col">СТО</th>
                    <th scope="col">Юрист</th>
                    <th scope="col">№ убытка</th>
                    <th scope="col">Клиент</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Авто</th>
                    <th scope="col">ГРЗ </th>
                    <th scope="col">Дата</th>
                    <th scope="col">Тип убытка</th>
                    <th scope="col">СТОА</th>
                    <template v-if="rout=='directions'">
                            <th scope="col">Сумма СК</th>
                            <th scope="col">Сумма З/Ч</th>
                            <th scope="col">Сумма ремонта</th>
                            <th scope="col">Затраты на ремонт</th>
                            <th scope="col">Оплата CК</th>
                            <th scope="col">Оплата СТО</th>
                            <th scope="col">Остаток</th>
                            <th scope="col"></th>
                       </template>



                </tr>
                <tr>
                    <th></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Статус" id="search-text1"
                            onkeyup="tableSearch(1,'applications')"></th>

                    <th scope="col"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text3"
                            onkeyup="tableSearch(3,'applications')">
                    </th>
                    <th scope="col"><input class="form-control" type="text" placeholder="МРК" id="search-text4"
                            onkeyup="tableSearch(4,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Город" id="search-text5"
                            onkeyup="tableSearch(5,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СК" id="search-text6"
                            onkeyup="tableSearch(6,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СТО" id="search-text7"
                            onkeyup="tableSearch(7,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Юрист" id="search-text8"
                            onkeyup="tableSearch(8,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="№ убытка " id="search-text9"
                            onkeyup="tableSearch(9,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="ФИО клиента"
                            id="search-text10" onkeyup="tableSearch(10,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Телефон" id="search-text11"
                            onkeyup="tableSearch(11,'applications')"></th>


                    <th scope="col"><input class="form-control" type="text" placeholder="Авто " id="search-text12"
                            onkeyup="tableSearch(12,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="ГРЗ" id="search-text13"
                            onkeyup="tableSearch(13,'applications')"> </th>
                    <th scope="col"></th>


                    <th scope="col"><input class="form-control" type="text" placeholder="Тип убытка"
                            id="search-text14" onkeyup="tableSearch(14,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СТОА" id="search-text15"
                            onkeyup="tableSearch(15,'applications')"></th>
                            <template v-if="rout=='directions'">


                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>

                            </template>

                </tr>
            </thead>
            <tbody>
                <tr v-for="application in applications" :key="application.id" @click="openApplication(application.id)">
                    <td>
                        <template v-if="application.doc">
                            <div class="td_round  yes"> да
                                </div>
                        </template>
                        <template v-else>
                            <div class="td_round  no"> нет
                                </div>
                        </template>
                    </td>
                    <td>
                        <div :class="'td_round '+application.status_theme">
                                {{ application.StatusName }}
                        </div>
                    </td>
                    <td>
                         <div :class="'progressbar '+ (application.count_hour > 0 ? 'blue' : 'red') ">
                                    <span
                                        :style="'width: '+((application.count_hour < application.max_hour ? application.count_hour : application.max_hour) / application.max_hour) * 100 +'%'">{{ application.count_hour > 0 ? application.count_hour : 'просрочено' }}
                                        <span>
                                            <template v-if="(application.id_status > 11 && application.id_status != 18)" >
                                                {{ showDays(application.count_hour) }}
                                            </template>
                                            <template v-else>
                                                {{ showHours(application.count_hour) }}
                                            </template>
                                        </span>
                                    </span>

                                </div>
                                <!-- @if (application.id_status < 5)
                                    {{-- <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ (application.count_five / 5) * 100 }}%;"
                                            role="progressbar" aria-valuenow="{{ application.count_five }}"
                                            aria-valuemin="0" aria-valuemax="5">
                                            {{ application.count_five }} часов
                                        </div>
                                    </div> --}}
                                    <div class="progressbar {{ application.count_five > 0 ? '' : 'red' }}">
                                        <span
                                            style="width: {{ (application.count_five / 5) * 100 }}%">{{ application.count_five > 0 ? application.count_five : 'просрочено' }}
                                            <span>{{ show_hours(application.count_five) }}</span></span>
                                    </div>
                                @endif -->


                    </td>
                    <td>
                        <notify-counter :id_app="application.id" :user="user_id"
                                :count_in="application.count" />
                    </td>

                    <td>{{ application.mrc }}</td>
                    <td>{{ application.city }}</td>
                    <td>{{ application.sk }}</td>
                    <td>{{ application.sto }}</td>
                    <td>{{ application.urist }}</td>
                    <td>{{ application.number_of_claim }}</td>
                    <td>{{ application.client_name }}</td>
                    <td>{{ application.client_phone }}</td>
                    <td>{{ application.car_brand }}</td>
                    <td>{{ application.car_number }}</td>
                    <td>{{ application.date }}</td>
                    <td>{{ application.type_of_claim }}</td>
                    <td>{{ application.stoa }}</td>
                </tr>
            </tbody>
            </table>


</div>
</template>

<script>

import NotifyCounter from '../components/NotifyCounter.vue';
export default {
        components: {
            NotifyCounter,
        },
        props:[
            'user_id',
            'in_applications',
            'rout',
        ],

        data(){

            return{
                applications: this.in_applications,

            }
        },


    methods: {
        openApplication(id){
            window.location.href = '/application/' + id
            return false
        },
        showDays(count_days)
        {
            count_days = count_days % 100;
        if (count_days > 10 && count_days < 20)
            return ' дней';
        count_days = count_days % 10;
        if (count_days == 1) {
            return ' день';
        }
        if (count_days >= 2 && count_days <= 4) {
            return ' дня';
        }
        return ' дней';
        },
        showHours(count_hours) {
             count_hours = count_hours % 100;
        if (count_hours > 10 && count_hours < 20)
            return ' часов';
        count_hours = count_hours % 10;
        if (count_hours == 1) {
            return ' час';
        }
        if (count_hours >= 2 && count_hours <= 4) {
            return ' часа';
        }
        return ' часов';

        },

        tableSearch(columnNumber, tableId) {
            let input, filter, table, tr, td, i, txtValue;
            input = document.getElementById('search-text'+columnNumber);
            filter = input.value.toUpperCase();
            table = document.getElementById(tableId);
            tr = table.getElementsByTagName('tr');

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName('td')[columnNumber - 1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

    }

	}

</script>
