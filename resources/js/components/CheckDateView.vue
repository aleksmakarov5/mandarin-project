<style>
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
</style>
<template>
    <a @click.prevent="showDefault = true">
        <div :class="'td_round ' + status.status_theme">
            {{ status.StatusName }}
        </div>
    </a>
    <div>
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
    </div>
</template>
<script>
export default {
    props: ["id_app", "status", "app_status"],
    data() {
        return {
            view_date: "",
            showDefault: false,
        };
    },
    methods: {
        checkAppStatus() {
            if (this.app_status == 14) {
                return "Дефектовка проведена, Т/С выдан клиенту до поступления запчастей";
            } else {
                return "Клиент вызван на ремонт";
            }
        },
        updateDate() {
            axios
                .post(`/api/application/update_view_date`, {
                    id: this.id_app,
                    view_date: this.view_date,
                })

                .then((res) => {
                    location.reload();
                });
        },
    },
};
</script>
