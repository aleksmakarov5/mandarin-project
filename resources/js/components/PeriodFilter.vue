<style></style>
<template>
    <table class="table">
        <tr>
            <th>Начало периода</th>
            <th>Конец периода</th>
            <th rowspan="2">
                <Button class="nav-link" @click="update_filter()"
                    >Показать</Button
                >
            </th>
        </tr>
        <tr>
            <td>
                <input type="date" v-model="start_date" />
            </td>
            <td>
                <input type="date" v-model="end_date" />
            </td>
        </tr>
    </table>
</template>
<script>
export default {
    props: ["id_user", "period_filter_in"],

    data() {
        return {
            start_date: this.period_filter_in.start_date,
            end_date: this.period_filter_in.end_date,
        };
    },

    mounted() {},
    methods: {
        update_filter() {
            axios
                .post(`/api/application/period_filter`, {
                    id_user: this.id_user,
                    start_date: this.start_date,
                    end_date: this.end_date,
                })
                .then((res) => {
                    location.reload();
                });
        },
    },
};
</script>
