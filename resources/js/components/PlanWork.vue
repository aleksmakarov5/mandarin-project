<template>
    <div>
        <button class="accordion-est" @click="show = !show">
            Плановые работы
        </button>
        <div :class="show ? '' : 'd_none'">
            <table class="troubleshooting">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(work, index) in works">
                        <tr @click="changeEditWorkId(work.id, work.name)">
                            <td scope="col">
                                {{ index + 1 }}
                            </td>
                            <td
                                :id="'name_' + work.id"
                                contenteditable
                                @blur="editName(work.id)"
                                @keydown.enter="editName(work.id)"
                            >
                                {{ work.name }}
                            </td>

                            <td scope="col">
                                <a @click.prevent="deletework(work.id)">
                                    <img
                                        src="/storage/img/delete.png"
                                        width="30"
                                        alt="delete"
                                /></a>
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td scope="col"></td>
                        <td>
                            <textarea
                                cols="400"
                                id="name"
                                v-model="name_Cr"
                                class="w-100"
                            ></textarea>
                        </td>

                        <td scope="col">
                            <a @click.prevent="createWork()">
                                <img
                                    src="/storage/img/edit.png"
                                    width="30"
                                    alt="create"
                            /></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ["app_id", "in_summ_service", "in_works"],

    data() {
        return {
            works: this.in_works,
            id_app: this.app_id,
            EditWorkId: null,
            name: "",
            price_service: "",
            name_Cr: "",
            price_service_Cr: "",
            summ_service: this.in_summ_service,
            show: true,
        };
    },

    mounted() {
        // this.getWorks()
    },
    methods: {
        getWorks(res) {
            this.works = res.data;
            console.log(res);
        },
        deletework(id) {
            if (confirm("Точно удалить?")) {
                axios.delete(`/api/planwork/${id}`).then((res) => {
                    this.getWorks(res);
                });
            }
        },
        changeEditWorkId(id, name, price_service) {
            this.EditWorkId = id;
            this.name = name;
            this.price_service = price_service;
        },
        isEdit(id) {
            return this.EditWorkId === id;
        },
        updateWork(id) {
            axios
                .post(`/api/planwork/${id}/update`, {
                    id_application: this.app_id,
                    name: this.name,
                    price_service: this.price_service,
                    summ: 0,
                })
                .then((res) => {
                    this.getWorks(res);
                });
            this.changeEditWorkId(null);
        },
        createWork() {
            axios
                .post(`/api/planwork/create`, {
                    id_application: this.app_id,
                    name: this.name_Cr,
                })
                .then((res) => {
                    this.getWorks(res);
                });
            this.name_Cr = "";
        },

        editName(id) {
            let name = document.getElementById("name_" + id);
            this.name = name.innerText;
            this.updateWork(id);
        },
    },
};
</script>
