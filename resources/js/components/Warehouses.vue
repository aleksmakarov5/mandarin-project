<style>
.tbl {
    width: 100%;
}
.face {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    padding: 20px;
    overflow-y: scroll;
    position: absolute;
    top: 10%;
    left: 10%;
    width: 80%;
    height: 80%;
    z-index: 7;
}
.back-face {
    overflow-y: scroll;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(128, 128, 128, 0.513);
}
</style>

<template>
    <table class="tbl">
        <tr>
            <td>
                <a class="nav-link">Приход</a>
            </td>
            <td>
                <a href="" class="nav-link">Расход</a>
            </td>
            <td>
                <a href="" class="nav-link">ОСВ</a>
            </td>
            <td>
                <inventory-items :inventory_items="inventory_items" />
            </td>
        </tr>
    </table>
    <table class="editable">
        <tr>
            <th>Кат. номер</th>
            <th>Название</th>
            <th>Авто</th>
            <th>Количество</th>
        </tr>
        <template v-for="(warehouse, i) in warehouses">
            <tr>
                <td colspan="4">
                    <h1>
                        <b>{{ i }} </b>
                    </h1>
                </td>
            </tr>

            <template v-for="(item, j) in warehouse">
                <tr v-if="itCount(item) > 0">
                    <td>{{ item[0].part_number }}</td>
                    <td>{{ item[0].part_name }}</td>
                    <td>{{ item[0].car_model }}</td>
                    <td align="center">
                        <b>{{ itCount(item) }}</b>
                    </td>
                </tr>
            </template>
        </template>
    </table>
</template>

<script>
import InventoryItems from "../components/InventoryItems.vue";
import Counterparties from "../components/Counterparties.vue";

export default {
    components: {
        InventoryItems,
        Counterparties,
    },
    props: ["warehouses", "inventory_items", "counterparties"],
    data() {
        return {};
    },
    mounted() {},
    methods: {
        itCount(item) {
            let count = 0;
            for (let i = 0; i < item.length; i++) {
                count += item[i].count;
            }
            return count;
        },
    },
};
</script>
