<style></style>

<template>
    <table class="table">
        <tr>
            <td>
                <a href="" class="btn nav-link">Приход</a>
            </td>
            <td>
                <a href="" class="nav-link">Расход</a>
            </td>
            <td>
                <a href="" class="nav-link">ОСВ</a>
            </td>
            <td>
                <a href="" class="nav-link">Номенклатура</a>
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
