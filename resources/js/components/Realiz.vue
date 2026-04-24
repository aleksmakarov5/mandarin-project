<style></style>
<template>
    <a v-if="all" @click.prevent="all = !all" class="nav-link"
        >Показывать все</a
    >
    <a v-else @click.prevent="all = !all" class="nav-link"
        >Показывать невыгруженные</a
    >
    <table class="editable">
        <tr>
            <th>Название</th>
            <th>Сумма</th>
            <th>Организация</th>
            <th>Контрагент</th>
        </tr>
        <tr v-for="attachment in attachments">
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
    props: ["attachments"],
    data() {
        return {
            all: false,
        };
    },
    mounted() {
        console.log(this.attachments);
    },
    methods: {
        makeMoney(n) {
            if (!n) n = 0;
            return (
                parseFloat(n)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ") + " р."
            );
        },
    },
};
</script>
