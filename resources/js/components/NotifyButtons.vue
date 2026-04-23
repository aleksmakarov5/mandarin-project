<style>
.badge-yellow {
    background-color: rgb(245, 233, 16);
}
</style>
<template>
    <div class="badge-buttons">
        <p class="badge badge-blue" v-if="count.application != 0">
            {{ count.application }}
        </p>
        <p class="badge badge-red" v-if="count.message != 0">
            {{ count.message }}
        </p>
        <p class="badge badge-green" v-if="count.chat != 0">{{ count.chat }}</p>
        <p class="badge badge-yellow" v-if="count.app != 0">{{ count.app }}</p>
    </div>
</template>

<script>
export default {
    props: ["user", "pi", "count_in"],
    data() {
        return {
            count: this.count_in,
            user_id: this.user,
            p: this.pi,
        };
    },

    created() {
        Echo.channel("chat").listen("MessageSent", (e) => {
            this.getCounts();
        });
    },
    mounted() {
        console.log(this.count);
        // this.getCounts()
    },
    methods: {
        getCounts() {
            axios
                .get(`/api/notifycount/${this.p}/${this.user_id}`)
                .then((res) => {
                    this.count = res.data;
                });
        },
    },
};
</script>
