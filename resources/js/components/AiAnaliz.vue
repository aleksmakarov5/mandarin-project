<style>
.show_an {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    width: 100%;
    padding: 5px;
    overflow-y: scroll;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 10;
}
.close {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
    width: 20px;
    z-index: 8;
}
</style>
<template>
    <a href="" v-if="show_send" @click.prevent="sendAI()"
        ><img
            src="/storage/img/ai.png"
            width="30"
            title="ИИ Анализ фото. Перед анализом проверьте дату ДТП и ВИН"
        />
    </a>
    <a href="" v-else @click.prevent="">
        <img src="/storage/img/loading.png" width="30" title="Идет отправка" />
    </a>
    <div class="show_an" v-if="show_answer">
        <div class="close" @click="show_answer = !show_answer">
            <img src="/storage/img/close.png" width="20px" />
        </div>
        <br /><br />
        {{ text }}
    </div>
</template>

<script>
export default {
    props: ["app_id", "location", "user_id"],

    data() {
        return {
            show_send: true,
            show_answer: false,
            text: "",
        };
    },

    mounted() {},
    methods: {
        sendAI() {
            this.show_send = !this.show_send;
            axios
                .post("/api/ai/send_data", {
                    app_id: this.app_id,
                    location: this.location,
                    user_id: this.user_id,
                })
                .then((response) => {
                    this.show_answer = true;

                    if (response.data == 202)
                        this.text = "Анализ фото не проведен. Не заполнен VIN";
                    else if (response.data == 203)
                        this.text =
                            "Анализ фото не проведен. Не заполнена дата ДТП";
                    else if (response.data == 201)
                        this.text = "Анализ фото не проведен. Ошибка сервера";
                    else {
                        this.text =
                            "Данные для анализа отправлены. Ожидайте ответа в чате заявки";
                        console.log(response.data);
                        this.show_send = true;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
