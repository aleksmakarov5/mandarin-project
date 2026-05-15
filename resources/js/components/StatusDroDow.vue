<style>
.dro_dow_view {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: rgb(255, 255, 255);
    width: 150px;
    position: absolute;
    top: 20;
    left: 30;
    z-index: 10;
}
.d_round {
    margin:5px;
    border-radius: 6px;
    padding: 1px 3px;
    font-size: 10px;
    text-align: center;
    width: 99%;
}
.d_round:hover {
    cursor: pointer;
    width: 105%;
}
.main_window {
    position: fixed;
    top: 40%;
    left: 40%;
    background-color: rgba(255, 255, 255, 0.972);
    border-radius: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    justify-content: center;
    align-items: center;
    padding: 15px;
    z-index: 9999;
    color: black;
}
.main_window input {
    width: 100%;
    height: 40px;
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 10px;
    font-size: 16px;
    color: black;
    margin-bottom: 10px;
}
.main_window button {
    width: 100%;
    height: 40px;
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 5px;
    font-size: 16px;
    margin-bottom: 10px;
    background-color: #4caf50;
}
.close-main {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}
</style>
<template>
    <a @click.prevent="show_drop_down = !show_drop_down" class="nav-link"
        >Статус</a
    >
    <div
        class="back-face"
        @click="show_drop_down = !show_drop_down"
        v-if="show_drop_down"
    ></div>
    <div class="dro_dow_view" v-if="show_drop_down">
        <!-- Условие: $app->id_status < 12 -->
        <template v-if="app.id_status < 12">
            <!-- Условие: $app->id_insuranceCompany == 4 -->
            <div v-if="app.id_insuranceCompany == 4">
                <a @click.prevent="changeStatus(11, 'Изменить статус?')">
                    <div class="d_round" :class="status[10].status_theme">
                        {{ status[10].StatusName }}
                    </div>
                </a>
            </div>

            <div>
                <a @click.prevent="changeStatus(12, 'Изменить статус?')">
                    <div class="d_round" :class="status[11].status_theme">
                        {{ status[11].StatusName }}
                    </div>
                </a>
            </div>
        </template>

        <!-- Условие: $app->id_status == 12 || $app->id_status == 14 -->
        <template v-if="app.id_status == 12 || app.id_status == 14">
            <!-- Компонент check-date-view -->
            <div
                class="close-main"
                v-if="showDefault"
                @click="showDefault = !showDefault"
            ></div>
            <div class="main_window" v-if="showDefault">
                <h2>Укажите дату планируемого осмотра</h2>
                <div class="form-float">
                    <input
                        type="datetime-local"
                        v-model="view_date"
                        id="view_date"
                        class="form-cont"
                    />
                </div>
                <a @click="updateDate()" class="nav-link">
                    Сохранить и изменить статус
                </a>
            </div>

            <a
                @click.prevent="
                    app.view_date
                        ? changeStatus(13, 'Изменить статус?')
                        : (showDefault = true)
                "
                :title="
                    app.id_status == 14
                        ? 'Дефектовка проведена, Т/С выдан клиенту до поступления запчастей'
                        : 'Клиент вызван на ремонт'
                "
            >
                <div class="d_round" :class="status[12].status_theme">
                    {{ status[12].StatusName }}
                </div>
            </a>
        </template>
        <template v-if="app.id_status == 13">
            <div
                class="close-main"
                v-if="show13"
                @click="show13 = !show13"
            ></div>
            <div class="main_window" v-if="show13">
                <table>
                    <template v-if="!this.attach_statuses.includes(10)">
                        <tr>
                            <td>Загрузите фото приема автомобиля</td>
                            <td>
                                <file-upload
                                    :id_app="id"
                                    :status_id="10"
                                    :load_name="'Фото приема автомобиля'"
                                    :load_location="'Фото_приема_автомобиля'"
                                />
                            </td>
                        </tr>
                    </template>
                    <template v-if="!this.attach_statuses.includes(9)">
                        <tr>
                            <td>Загрузите Акт приема автомобиля</td>
                            <td>
                                <file-upload
                                    :id_app="id"
                                    :status_id="9"
                                    :load_name="'Акт приема автомобиля'"
                                    :load_location="'Акт_приема_автомобиля'"
                                />
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
            <a
                @click.prevent="
                    !this.attach_statuses.includes(9) ||
                    !this.attach_statuses.includes(10)
                        ? (show13 = true)
                        : changeStatus(14, 'Изменить статус?')
                "

            >
                <div :class="`d_round ${status[13].status_theme}`">
                    {{ status[13].StatusName }}
                </div>
            </a>
        </template>

            <template v-if="app.id_status == 14">
                <div
                    class="close-main"
                    v-if="show14"
                    @click="show14 = !show14"
                ></div>
                <div class="main_window" v-if="show14">
                    <table>
                        <template v-if="!this.attach_statuses.includes(30)">
                            <tr>
                                <td>Загрузите фото деффектовки</td>
                                <td>
                                    <file-upload
                                        :id_app="id"
                                        :status_id="30"
                                        :load_name="'Фото деффектовки'"
                                        :load_location="'Фото_деффектовки'"
                                    />
                                </td>
                            </tr>
                        </template>
                        <template v-if="!app.sp_date && summ_order">
                            <tr>
                                <td>Введите дату поставки З/Ч</td>
                                <td>
                                    <div class="form-float">
                                        <input
                                            type="datetime-local"
                                            v-model="sp_date"
                                            id="sp_date"
                                            class="form-cont"
                                        />
                                    </div>
                                </td>
                                <td>
                                    <a
                                        @click.prevent="updateSpDate()"
                                        class="nav-link"
                                        >Готово</a
                                    >
                                </td>
                            </tr>
                        </template>
                        <template v-if="app.id_lawyer <= 2">
                            <template v-if="!this.attach_statuses.includes(3)">
                                <tr>
                                    <template v-if="with_dogovor">
                                        <td>
                                            Загрузите скан договора с клиентом
                                        </td>
                                        <td>
                                            <file-upload
                                                :id_app="id"
                                                :status_id="3"
                                                :load_name="'Договор с клиентом'"
                                                :load_location="'Договор_с_клиентом'"
                                            />
                                        </td>
                                    </template>
                                    <template v-else>
                                        Введите комментарий (более 10 символов)
                                        <br></br>
                                        <textarea v-model="comment" rows="5" cols="40"></textarea>
                                        <a
                                            v-if="comment.length > 10"
                                            class="nav-link"
                                            @click.prevent="saveComment(15)"
                                            >Сохранить</a
                                        >
                                    </template>
                                    <td
                                        v-if="
                                            this.attach_statuses.includes(30) &&
                                            app.sp_date &&
                                            with_dogovor
                                        "
                                        rowspan="2"
                                    >
                                        <a
                                            class="nav-link"
                                            @click="with_dogovor = false"
                                            >Без договора</a
                                        >
                                    </td>
                                </tr>
                            </template>
                            <template
                                v-if="
                                    !this.attach_statuses.includes(5) &&
                                    with_dogovor
                                "
                            >
                                <tr>
                                    <td>
                                        Загрузите скан доп соглашения с клиентом
                                    </td>
                                    <td>
                                        <file-upload
                                            :id_app="id"
                                            :status_id="5"
                                            :load_name="'Дополнительное соглашение с клиентом'"
                                            :load_location="'Дополнительное_соглашение_с_клиентом'"
                                        />
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </table>
                </div>
                <template v-if="app.id_lawyer > 2">
                    <a
                        @click.prevent="
                            !this.attach_statuses.includes(30) || (!app.sp_date && summ_order)
                                ? (show14 = true)
                                : changeStatus(summ_order!=0?15:17, 'Изменить статус?')
                        "
                        title="Заказ З/Ч"
                    >
                        <div :class="`d_round ${status[summ_order!=0?14:16].status_theme}`">
                            {{ status[summ_order!=0?14:16].StatusName }}
                        </div>
                    </a>
                </template>
                <template v-else>
                    <a
                        @click.prevent="
                            !this.attach_statuses.includes(30) ||
                            !this.attach_statuses.includes(3) ||
                            !this.attach_statuses.includes(5) ||
                            (!app.sp_date && summ_order)
                                ? (show14 = true)
                                : changeStatus(summ_order!=0?15:17, 'Изменить статус?')
                        "
                        title="Заказ З/Ч"
                    >
                        <div :class="`d_round ${status[summ_order!=0?14:16].status_theme}`">
                            {{ status[summ_order!=0?14:16].StatusName }}
                        </div>
                    </a>
                </template>
            </template>
            <template v-if="app.id_status == 15 || app.id_status == 16">
                <a @click.prevent="changeStatus(app.id_status+1, 'Изменить статус?')">
                    <div :class="'d_round '+status[app.id_status].status_theme">
                        {{ status[app.id_status].StatusName }}
                    </div>
                         </a>
            </template>
 <template v-if="app.id_status == 17">
            <div
                class="close-main"
                v-if="show17"
                @click="show17 = !show17"
            ></div>
            <div class="main_window" v-if="show17">
                <table>
                    <template v-if="!this.attach_statuses.includes(15)">
                        <tr>
                            <td>Загрузите фото сдачи автомобиля</td>
                            <td>
                                <file-upload
                                    :id_app="id"
                                    :status_id="15"
                                    :load_name="'Фото сдачи автомобиля'"
                                    :load_location="'Фото_сдачи_автомобиля'"
                                />
                            </td>
                        </tr>
                    </template>
                    <template v-if="!this.attach_statuses.includes(14)">
                        <tr>
                            <td>Загрузите Акт приема автомобиля</td>
                            <td>
                                <file-upload
                                    :id_app="id"
                                    :status_id="14"
                                    :load_name="'Акт сдачи автомобиля'"
                                    :load_location="'Акт_сдачи_автомобиля'"
                                />
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
            <a
                @click.prevent="
                    !this.attach_statuses.includes(14) ||
                    !this.attach_statuses.includes(15)
                        ? (show17 = true)
                        : changeStatus(18, 'Изменить статус?')
                "

            >
                <div :class="`d_round ${status[17].status_theme}`">
                    {{ status[17].StatusName }}
                </div>
            </a>
        </template>

<template v-if="app.id_status == 18">


            <a
                @click.prevent="
                    !this.app.ic_pay_date ||
        !this.app.sto_pay_date ||
                    !this.app.zp_date
                        ? showAlert('Заполните таблицу с датами оплат!')
                        : changeStatus(25, 'Изменить статус?')
                "
            >
                <div :class="`d_round ${status[24].status_theme}`">
                    {{ status[24].StatusName }}
                </div>
            </a>
        </template>
        <template v-if="app.id_status == 27">


            <a
                @click.prevent="

                        changeStatus(1, 'Изменить статус?')
                "
            >
                <div :class="`d_round ${status[1].status_theme}`">
                    Вернуть в работу
                </div>
            </a>
        </template>
        <!-- Блок для id_insuranceCompany == 4 -->
  <template v-if="app.id_insuranceCompany == 4">
    <div :class="`d_round ${status[19].status_theme}`">----</div>

    <!-- Цикл для статусов 101–111 -->
    <template v-for="i in 11" :key="`status-${i + 100}`">
      <a
        @click.prevent="changeStatus(i + 100, 'Изменить статус?')"

      >
        <div :class="`d_round ${f_status[i - 1].FinStatusTheme}`">
          {{ f_status[i - 1].FinStatusName }}
        </div>
      </a>
    </template>
  </template>

  <!-- Блоки для finance_statuses_id -->
  <template v-else>
    <template v-if="[3, 5, 7, 9, 10].includes(app.finance_statuses_id)">
      <a
        @click.prevent="changeStatus(app.finance_statuses_id + 100, 'Изменить статус?')"
      >
         <div :class="`d_round ${f_status[app.finance_statuses_id-1].FinStatusTheme}`">
          {{ f_status[app.finance_statuses_id-1].FinStatusName }}
        </div>
      </a>
    </template>
  </template>
</div>
</template>

<script>
import FileUpload from "./FileUpload.vue";
export default {
    components: { FileUpload },
    props: ["id", "app", "status", "f_status", "attach_statuses", "summ_order","user_id"],
    data() {
        return {
            show_drop_down: false,
            showDefault: false,
            view_date: "",
            show13: false,
            show14: false,
            show17: false,
            sp_date: "",
            with_dogovor: true,
            comment: "",
        };
    },
    methods: {
        changeStatus(statusId, confirmationMsg = "Изменить статус?") {
            if (!confirm(confirmationMsg)) return;
            // Отправка запроса на изменение статуса
            axios
                .get(`/applications/${this.id}/change_st/${statusId}`)
                .then((response) => {
                    location.reload();
                })
                .catch((error) => console.error("Error:", error));
        },
        showAlert(message) {
            alert(message);
        },
        updateDate() {
            axios
                .post(`/api/application/update_view_date`, {
                    id: this.id,
                    view_date: this.view_date,
                })

                .then((res) => {
                    location.reload();
                });
        },
        updateSpDate() {
            axios
                .post(`/api/application/update_sp_date`, {
                    id: this.id,
                    sp_date: this.sp_date,
                })

                .then((res) => {
                    location.reload();
                });
        },
        saveComment(id_status) {
            axios
                .post(`/api/application/update_comment`, {
                    id: this.id,
                    comment: this.comment,
                    user_id: this.user_id,
                    id_status: id_status,
                })

                .then((res) => {
                    location.reload();
                });
        },
    },
    mounted() {
        this.view_date = this.app.view_date;
        this.sp_date = this.app.sp_date;
    },
};
</script>
