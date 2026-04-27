<style>
.dro_dow_view {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: rgb(255, 255, 255);
    width: 150px;
    padding: 5px;
    position: absolute;
    top: 20;
    left: 30;
    z-index: 10;
}
.d_round {
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
    top: 20%;
    left: 50%;
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
                <a @click="updateDate" class="nav-link">
                    Сохранить и изменить статус
                </a>
            </div>

            <a
                @click.prevent="
                    app.view_date
                        ? changeStatus(13, 'Изменить статус?')
                        : showAlert(
                              'Необходимо на вкладке <<Основная информация>> \n указать дату планируемого осмотра'
                          )
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

        <template v-if="app.id_insuranceCompany == 4">
            <template>
                <div class="d_round" :class="status[19].status_theme">----</div>
            </template>
            <template
                v-for="(item, index) in f_status.slice(0, 11)"
                :key="index"
            >
                <a
                    @click.prevent="
                        changeStatus(index + 101, 'Изменить статус?')
                    "
                >
                    <div class="d_round" :class="item?.FinStatusTheme">
                        {{ item?.FinStatusName }}
                    </div>
                </a>
            </template>
        </template>
        <template v-else>
            <!-- Условия для finance_statuses_id -->
            <template v-if="[3, 5, 7, 9, 10].includes(app.finance_statuses_id)">
                <a
                    @click.prevent="
                        app.finance_statuses_id == 3
                            ? changeStatus(104, 'Изменить статус?')
                            : app.finance_statuses_id == 5
                            ? changeStatus(106, 'Изменить статус?')
                            : app.finance_statuses_id == 7
                            ? changeStatus(108, 'Изменить статус?')
                            : app.finance_statuses_id == 9 ||
                              app.finance_statuses_id == 10
                            ? app.ic_pay_date
                                ? changeStatus(111, 'Изменить статус?')
                                : showAlert('Укажите дату оплаты!')
                            : changeStatus(111, 'Изменить статус?')
                    "
                >
                    <div
                        class="d_round"
                        :class="
                            app.finance_statuses_id == 3
                                ? f_status[3].FinStatusTheme
                                : app.finance_statuses_id == 5
                                ? f_status[5].FinStatusTheme
                                : app.finance_statuses_id == 7
                                ? f_status[7].FinStatusTheme
                                : f_status[app.finance_statuses_id]
                                      ?.FinStatusTheme
                        "
                    >
                        {{
                            app.finance_statuses_id == 3
                                ? f_status[3].FinStatusName
                                : app.finance_statuses_id == 5
                                ? f_status[5].FinStatusName
                                : app.finance_statuses_id == 7
                                ? f_status[7].FinStatusName
                                : f_status[app.finance_statuses_id]
                                      .FinStatusName
                        }}
                    </div>
                </a>
            </template>
        </template>

        <!-- Административные статусы -->
    </div>
</template>

<script>
export default {
    props: ["id", "app", "status", "f_status", "attach_statuses"],
    data() {
        return {
            show_drop_down: false,
            showDefault: false,
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
    },
};
</script>
