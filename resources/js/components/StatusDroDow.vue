<style>
.drop_down_view {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    width: 250px;
    padding: 5px;
    position: absolute;
    top: 20;
    left: 30;
    z-index: 10;
    align: left;
}
.td-round:hover {
    cursor: pointer;
}
</style>
<template>
    <a
        @click.prevent="show_drop_down = show_drop_down = !show_drop_down"
        class="nav-link"
        >Статус</a
    >
    <div
        class="back-face"
        @click="show_drop_down = !show_drop_down"
        v-if="show_drop_down"
    ></div>
    <div class="drop_down_view" v-if="show_drop_down">
        sadfasfdsa
        <!-- Условие: $app->id_status < 12 -->
        <template v-if="app.id_status < 12">
            <!-- Условие: $app->id_insuranceCompany == 4 -->
            <div v-if="app.id_insuranceCompany === 4">
                <a @click.prevent="changeStatus(11, 'Изменить статус?')">
                    <div class="td_round" :class="status[10].status_theme">
                        {{ status[10].StatusName }}
                    </div>
                </a>
            </div>

            <div>
                <a @click.prevent="changeStatus(12, 'Изменить статус?')">
                    <div class="td_round" :class="status[11].status_theme">
                        {{ status[11].StatusName }}
                    </div>
                </a>
            </div>
        </template>

        <!-- Условие: $app->id_status == 12 || $app->id_status == 14 -->

        <!-- Административные статусы -->
    </div>
</template>

<script>
export default {
    props: ["id", "app", "status", "f_status", "attach_statuses"],
    data() {
        return {
            show_drop_down: false,
        };
    },
    methods: {
        changeStatus(statusId, confirmationMsg = "Изменить статус?") {
            if (!confirm(confirmationMsg)) return;
            // Отправка запроса на изменение статуса
            fetch(`/applications/${this.id}/change_st/${statusId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => response.json())
                .catch((error) => console.error("Error:", error));
        },
        showAlert(message) {
            alert(message);
        },
    },
};
</script>
