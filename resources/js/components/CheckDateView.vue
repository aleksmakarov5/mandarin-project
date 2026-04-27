<style></style>
<template></template>
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
