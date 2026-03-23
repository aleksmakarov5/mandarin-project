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
</style>
<template>
    <a
        @click.prevent="show_drop_down = !show_drop_down"
        href=""
        class="nav-link"
        >{{ name }}</a
    >
    <div
        class="back-face"
        @click="show_drop_down = !show_drop_down"
        v-if="show_drop_down"
    ></div>
    <diw class="drop_down_view" v-if="show_drop_down">
        <span v-for="(item, i) in items"
            ><a :href="'/' + i" class="nav-link">{{ item }}</a></span
        >
    </diw>
</template>

<script>
export default {
    props: ["name", "items"],

    data() {
        return {
            show_drop_down: false,
        };
    },

    mounted() {},
    methods: {},
};
</script>
