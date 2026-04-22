<template>
    <div align="center">
        <table class="troubleshooting">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Наименование детали автомобиля</th>
                    <th scope="col">Повреждение</th>

                    <th scope="col">Требуемое ремонтное воздействие</th>
                    <th scope="col">Название фото</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(troubleshooting, index) in troubleshootings">
                    <tr>
                        <td scope="col">
                            {{ index + 1 }}
                        </td>
                        <td
                            :id="'Part_Name_' + troubleshooting.id"
                            contenteditable
                            @blur="editPartName(troubleshooting.id, index)"
                            @keydown.enter="
                                editPartName(troubleshooting.id, index)
                            "
                        >
                            {{ troubleshooting.PartName }}
                        </td>
                        <td
                            :id="'Сonclusion_' + troubleshooting.id"
                            contenteditable
                            @blur="editСonclusion(troubleshooting.id, index)"
                            @keydown.enter="
                                editСonclusion(troubleshooting.id, index)
                            "
                        >
                            {{ troubleshooting.Сonclusion }}
                        </td>
                        <td
                            :id="'Work_' + troubleshooting.id"
                            contenteditable
                            @blur="editWork(troubleshooting.id, index)"
                            @keydown.enter="editWork(troubleshooting.id, index)"
                        >
                            {{ troubleshooting.Work }}
                        </td>
                        <td
                            :id="'img_' + troubleshooting.id"
                            contenteditable
                            @blur="editImg(troubleshooting.id, index)"
                            @keydown.enter="editImg(troubleshooting.id, index)"
                        >
                            {{ troubleshooting.img }}
                        </td>

                        <td
                            scope="col"
                            @click.prevent="
                                deletetroubleshooting(troubleshooting.id)
                            "
                        >
                            <img
                                src="/storage/img/delete.png"
                                width="30"
                                alt="delete"
                            />
                        </td>
                    </tr>
                </template>
                <tr>
                    <td scope="col" colspan="5" align="center">
                        <a @click.prevent="createTroubleshooting()">
                            <img
                                src="/storage/img/add.png"
                                width="30"
                                alt="create"
                        /></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    emit: ["getTroubleshutings"],
    props: ["app_id", "in_troubleshootings", "in_img"],

    data() {
        return {
            troubleshootings: this.in_troubleshootings,
            index: 0,
            id_app: this.app_id,
            EditTroubleshootingId: null,
            PartName: "",
            Work: "",
            Сonclusion: "",
            PartName_Cr: " ",
            Work_Cr: " ",
            Сonclusion_Cr: " ",
            img: "",
            img_cr: this.in_img,
        };
    },

    mounted() {
        // this.getTroubleshutings();
    },
    methods: {
        getTroubleshutings(res) {
            this.troubleshootings = res.data;
            this.$emit("getTroubleshutings", this.troubleshootings);
            console.log(res.data);
        },
        deletetroubleshooting(id) {
            if (confirm("Точно удалить?")) {
                axios.delete(`/api/troubleshootings/${id}`).then((res) => {
                    this.getTroubleshutings(res);
                });
            }
        },
        changeEditTroubleshootingId(index) {
            this.PartName = this.troubleshootings[index].PartName;
            this.Work = this.troubleshootings[index].Work;
            this.Сonclusion = this.troubleshootings[index].Сonclusion;
            this.img = this.troubleshootings[index].img;
        },
        isEdit(id) {
            return this.EditTroubleshootingId === id;
        },
        updateTroubleshooting(id, index) {
            axios
                .post(`/api/troubleshootings/${id}/update`, {
                    id_application: this.app_id,
                    PartName: this.troubleshootings[index].PartName,
                    Work: this.troubleshootings[index].Work,
                    Сonclusion: this.troubleshootings[index].Сonclusion,
                    img: this.troubleshootings[index].img,
                })
                .then((res) => {
                    this.getTroubleshutings(res);
                });
        },
        createTroubleshooting() {
            axios
                .post(`/api/troubleshootings/create`, {
                    id_application: this.app_id,
                    PartName: this.PartName_Cr,
                    Work: this.Work_Cr,
                    Сonclusion: this.Сonclusion_Cr,
                    img: this.img_cr,
                })
                .then((res) => {
                    this.getTroubleshutings(res);
                });
        },

        editPartName(id, index) {
            let part_name = document.getElementById("Part_Name_" + id);
            this.troubleshootings[index].PartName = part_name.innerText;

            this.updateTroubleshooting(id, index);
        },
        editImg(id, index) {
            let Img = document.getElementById("img_" + id);
            this.troubleshootings[index].img = Img.innerText;

            this.updateTroubleshooting(id, index);
        },
        editСonclusion(id, index) {
            let сon = document.getElementById("Сonclusion_" + id);
            this.troubleshootings[index].Сonclusion = сon.innerText;

            this.updateTroubleshooting(id, index);
        },
        editWork(id, index) {
            let wor = document.getElementById("Work_" + id);
            this.troubleshootings[index].Work = wor.innerText;

            this.updateTroubleshooting(id, index);
        },
    },
};
</script>
