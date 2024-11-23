
<style type="text/css">
.inp-cont {
    display: block;
    width: 10px;
    font-family: inherit;
    margin: 0;
    font-size: 2rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
</style>
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
                    <th scope="col"><input class="inp-cont" type="text" placeholder="Статус" id="search-text2"
                            @keyup="tableSearch(2,'applications')"></th>

                    <th scope="col"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="" id="search-text4"
                            @keyup="tableSearch(4,'applications')">
                    </th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="МРК" id="search-text5"
                            @keyup="tableSearch(5,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="Город" id="search-text6"
                            @keyup="tableSearch(6,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="СК" id="search-text7"
                            @keyup="tableSearch(7,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="СТО" id="search-text8"
                            @keyup="tableSearch(8,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="Юрист" id="search-text9"
                            @keyup="tableSearch(9,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="№ убытка " id="search-text10"
                            @keyup="tableSearch(10,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="ФИО клиента"
                            id="search-text11" @keyup="tableSearch(11,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="Телефон" id="search-text12"
                            @keyup="tableSearch(12,'applications')"></th>


                    <th scope="col"><input class="inp-cont" type="text" placeholder="Авто " id="search-text13"
                            @keyup="tableSearch(13,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="ГРЗ" id="search-text14"
                            @keyup="tableSearch(14,'applications')"> </th>
                    <th scope="col"></th>


                    <th scope="col"><input class="inp-cont" type="text" placeholder="Тип убытка"
                            id="search-text14" @keyup="tableSearch(16,'applications')"></th>
                    <th scope="col"><input class="inp-cont" type="text" placeholder="СТОА" id="search-text17"
                            @keyup="tableSearch(17,'applications')"></th>
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
                <tr v-for="application in applications" :key="application.created_at" @click="openApplication(application.id)">
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
                    <template v-if="application.count_hour<=0">


                            <td scope="col" class="alert alert-danger">
                                Просрочено
                            </td>
                        </template>
                        <template v-else>
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
                                <template v-if="application.id_status < 5">

                                    <div :class="'progressbar '+(application.count_five > 0 ? '' : 'red' )">
                                        <span
                                            :style="'width: '+ ((application.count_five / 5) * 100 )+'%'">{{ (application.count_five > 0 ? showHours(application.count_five)  : 'просрочено') }}
                                            </span>
                                    </div>
                                </template>
                            </td>
                        </template>


                    <td>
                        <notify-counter :id_app="application.id" :user="user_id"
                                :count_in="application.count" />
                    </td>

                    <td>{{ mrkFam(application.MRK_name) }}</td>
                    <td>{{ application.city }}</td>
                    <td><div :class="'td_round '+application.insurance_companies_theme">{{ application.name }}</div></td>
                    <td><div :class="'td_round '+application.services_theme">{{ application.service_name }}</div></td>
                    <td><div :class="'td_round '+lawyersColor(application.lawyers_color)">{{ mrkFam(application.lawyer_name) }} </div></td>
                    <td>{{ application.numberInsuranceCompanyApplication }}</td>
                    <td>{{ application.ClientName }}</td>
                    <td>{{ application.ClientPhone }}</td>
                    <td>{{ application.CarModel }}</td>
                    <td>{{ application.gnz }}</td>
                    <td>{{ formatDate(application.created_at) }}</td>
                    <td>{{ application.ub_type?'Каско':'Осаго' }}</td>
                    <td>{{ application.our_name }}</td>
                    <template v-if="rout=='directions'">
                                <td>
                                    {{ application.summ_ic_full }}</td>
                                <td>
                                    {{ application.summ_order }}</td>
                                <td>
                                    {{ application.summ_service }}</td>
                                <td>
                                    {{ application.summ_full }}</td>

                                <td>
                                    {{ formatDate(application.ic_pay_date) }}
                                </td>
                                <td>
                                    {{ formatDate(application.sto_pay_date) }}
                                </td>
                                <td>
                                    <div
                                        :class="application.proc > 61?'alert alert-success':'alert alert-danger'">
                                        {{ application.eco_delta_full }} ({{ application.proc }}%)</div>
                                </td>
                    </template>
                    <td>
                        <a class="nav-link" @click="deleteApplication(application.id)">
                            <img
                                    src="/storage/img/delete.png" width="20" alt="delete">
                        </a>
                    </td>
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
                applications:[],
                indexes: Object.keys(this.in_applications).reverse(),

            }
    },
    created() {
        for (let i = 0; i < this.indexes.length; i++)
        {

             this.applications[i]=this.in_applications[this.indexes[i]]
        }
    },





    methods: {
        tableSearch(k, table){
            var phrase = document.getElementById('search-text' + k);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, 'i');
            var flag = false;
            for (var i = 2; i < table.rows.length; i++) {
                flag = false;
                flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
                if (flag) {
                    table.rows[i].style.display = "";
                } else {
                    table.rows[i].style.display = "none";
                }

            }
        },
        deleteApplication(id) {
            if (confirm('Точно удалить заявку №' + id + '?')) {
                axios.delete('/api/application/' + id) .then(res => {
                    this.applications = this.applications.splice(this.applications.indexOf(id), 1);
                })
            }
            else
            return false

        },
        formatDate(date)
        {
            let options = {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            };
            if(date)
                return new Date(date).toLocaleString('ru', options);
        else return ''
        },
        lawyersColor(color)
        {
            if (color)
                return 'no'
            else
                return 'yes'

        },
        mrkFam(surname) {
            let text=surname?surname:' '
        return text.substring(0,text.indexOf(' '))
        },
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
