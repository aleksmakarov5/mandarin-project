<style></style>
<template>
    <table class="editable">
        <tr>
            <th>Название</th>
            <th>Сумма</th>
        </tr>
        <tr v-for="attachment in attachments">
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
        </tr>
    </table>
</template>
<script>
export default {
    props: ["attachments"],
    data() {
        return {};
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
