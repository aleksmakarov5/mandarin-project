<style></style>
<template>
    <a v-if="all" @click.prevent="editAll()" class="nav-link">Показывать все</a>
    <a v-else @click.prevent="editAll()" class="nav-link"
        >Показывать невыгруженные</a
    >
    <a
        v-if="selected_attachments.length && !creating"
        @click.prevent="makeArhive()"
        class="nav-link"
        >Подготовить архив</a
    >
    <a v-if="arhive" :href="arhive" class="nav-link">Скачать архив</a>
    <img v-if="creating" src="/storage/img/loading.png" alt="" width="50" />

    <table class="editable">
        <tr>
            <th>Название</th>
            <th>Сумма</th>
            <th>Организация</th>
            <th>Контрагент</th>
        </tr>
        <tr
            v-for="attachment in attachments"
            :style="docSelected(attachment.id)"
            @click="editAttach(attachment.id)"
        >
            <template v-if="!all">
                <td>
                    <a
                        :href="
                            'storage/files/' +
                            attachment.id_application +
                            '/' +
                            attachment.pdf_location
                        "
                        target="_blank"
                    >
                        <img src="storage/img/pdf.png" width="30px" />
                        {{ attachment.pdf_name }}</a
                    >
                </td>
                <td v-if="attachment.id_status == 39">
                    {{ makeMoney(attachment.price_repair_full) }}
                </td>
                <td v-else>
                    {{ makeMoney(attachment.add_pay) }}
                </td>
                <td>
                    {{ attachment.our_name }}
                </td>
                <td>
                    {{ attachment.ic_name }}
                </td>
            </template>
            <template v-else>
                <template v-if="!attachment.is_parsed">
                    <td>
                        <a
                            :href="
                                'storage/files/' +
                                attachment.id_application +
                                '/' +
                                attachment.pdf_location
                            "
                            target="_blank"
                        >
                            <img src="storage/img/pdf.png" width="30px" />
                            {{ attachment.pdf_name }}</a
                        >
                    </td>
                    <td v-if="attachment.id_status == 39">
                        {{ makeMoney(attachment.price_repair_full) }}
                    </td>
                    <td v-else>
                        {{ makeMoney(attachment.add_pay) }}
                    </td>
                    <td>
                        {{ attachment.our_name }}
                    </td>
                    <td>
                        {{ attachment.ic_name }}
                    </td>
                </template>
            </template>
        </tr>
    </table>
</template>
<script>
export default {
    props: ["in_attachments"],
    data() {
        return {
            attachments: this.in_attachments,
            all: false,
            selected_attachments: [],
            arhive: null,
            creating: false,
        };
    },
    mounted() {
        // console.log(this.attachments);
    },
    methods: {
        editAll() {
            this.all = !this.all;
            if (!this.all) {
                this.selected_attachments = [];
            } else {
                for (let i = 0; i <= this.attachments.length; i++) {
                    if (!this.attachments[i].is_parsed) {
                        this.selected_attachments.push(this.attachments[i].id);
                    }
                }
            }
        },
        makeMoney(n) {
            if (!n) n = 0;
            return (
                parseFloat(n)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ") + " р."
            );
        },
        editAttach(id) {
            this.arhive = null;
            if (this.selected_attachments.includes(id)) {
                this.selected_attachments.splice(
                    this.selected_attachments.indexOf(id),
                    1
                );
            } else {
                this.selected_attachments.push(id);
            }
        },
        docSelected(id) {
            if (this.selected_attachments.includes(id)) {
                return "background-color: lightblue; cursor: pointer;";
            } else {
                return " cursor: pointer;";
            }
        },
        makeArhive() {
            this.creating = true;
            axios
                .post("/api/application/makeArhive", {
                    attachments: this.selected_attachments,
                })
                .then((response) => {
                    this.arhive = response.data[0];
                    this.attachments = response.data[1];
                    this.selected_attachments = [];
                    this.creating = false;
                })
                .catch((error) => {
                    console.log(error);
                    this.creating = false;
                });
        },
    },
};
</script>
