<style>
.inp {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;
    background-color: white;
    width: 90%;
    height: 35px;
    font-size: 16px;
    padding: 12px;
    z-index: 12;
}
.inp-txt {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;
    background-color: white;
    width: 90%;
    height: 65px;
    font-size: 12px;
    padding: 12px;
    z-index: 12;
}

.labl {
    border-radius: 5px;
    padding: 7px;
    background-color: white;
    width: 90%;
    height: 35px;
    font-size: 16px;
}
.list {
    position: absolute;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;
    background-color: white;
    width: 70%;
    max-height: 60%;
    overflow-y: scroll;
    padding: 10px;
}
</style>

<template>
    <a class="nav-link" @click.prevent="showIncome = !showIncome">Расход</a>
    <div
        v-if="showIncome"
        class="back-face"
        @click="showIncome = !showIncome"
    ></div>

    <div v-if="showIncome" class="face">
        <div class="form-group">
            <label class="labl">Дата</label>
            <input type="date" class="inp" v-model="created_at" />
        </div>
        <div class="form-group">
            <label class="labl">Склад</label>
            <div class="inp" @click="warehouses_show = !warehouses_show">
                <template v-if="warehouse_id">
                    <template v-for="item in warehouses">
                        <template v-if="item.id == warehouse_id">
                            <span>{{ item.warehouse_name }} </span>
                        </template>
                    </template>
                </template>
            </div>
            <div
                v-if="warehouses_show"
                class="back-face"
                @click="warehouses_show = !warehouses_show"
            ></div>
            <div class="list" v-if="warehouses_show">
                <table class="font-table" id="warehouses">
                    <tr>
                        <th>Название</th>
                    </tr>
                    <tr>
                        <th>
                            <input
                                id="warehouses0"
                                type="text"
                                placeholder="Название"
                                @keyup="tableSearch(0, 'warehouses')"
                            />
                        </th>
                    </tr>
                    <tr
                        v-for="item in warehouses"
                        @click="selectWarehouse(item.id)"
                    >
                        <td>{{ item.warehouse_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div v-if="warehouse_id">
            <div class="form-group">
                <label class="labl">Товар</label>
                <div class="inp" @click="items_show = !items_show">
                    <template v-if="inventory_item_id">
                        <template v-for="(item, j) in inventoryItems">
                            <template v-if="j == inventory_item_id">
                                <span
                                    >{{ item[0].part_number }} |
                                    {{ item[0].part_name }} |
                                    {{ item[0].car_model }}</span
                                >
                            </template>
                        </template>
                    </template>
                </div>
                <div
                    v-if="items_show"
                    class="back-face"
                    @click="items_show = !items_show"
                ></div>
                <div class="list" v-if="items_show">
                    <table class="font-table" id="items">
                        <tr>
                            <th>Кат. номер</th>
                            <th>Наименование</th>
                            <th>Авто</th>
                            <th>Остаток</th>
                        </tr>
                        <tr>
                            <th>
                                <input
                                    id="items0"
                                    type="text"
                                    placeholder="Кат. номер"
                                    @keyup="tableSearch(0, 'items')"
                                />
                            </th>
                            <th>
                                <input
                                    id="items1"
                                    type="text"
                                    placeholder="Название"
                                    @keyup="tableSearch(1, 'items')"
                                />
                            </th>
                            <th>
                                <input
                                    id="items2"
                                    type="text"
                                    placeholder="Авто"
                                    @keyup="tableSearch(2, 'items')"
                                />
                            </th>
                        </tr>
                        <template v-for="(item, j) in inventoryItems">
                            <tr v-if="itCount(item) > 0" @click="selectItem(j)">
                                <td>{{ item[0].part_number }}</td>
                                <td>{{ item[0].part_name }}</td>
                                <td>{{ item[0].car_model }}</td>
                                <td>{{ itCount(item) }}</td>
                            </tr>
                        </template>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label class="labl">Количество</label>
                <input type="number" class="inp" v-model.number="count" />
            </div>

            <div class="form-group">
                <label class="labl">Заказчик</label>
                <div
                    class="inp"
                    @click="counterpartie_show = !counterpartie_show"
                >
                    <template v-if="counterpartie_id">
                        <template v-for="item in counterparties">
                            <template v-if="item.id == counterpartie_id">
                                <span>{{ item.name }} </span>
                            </template>
                        </template>
                    </template>
                </div>
                <div
                    v-if="counterpartie_show"
                    class="back-face"
                    @click="counterpartie_show = !counterpartie_show"
                ></div>
                <div class="list" v-if="counterpartie_show">
                    <table class="font-table" id="counterparties">
                        <tr>
                            <th>Название</th>
                        </tr>
                        <tr>
                            <th>
                                <input
                                    id="counterparties0"
                                    type="text"
                                    placeholder="Название"
                                    @keyup="tableSearch(0, 'counterparties')"
                                />
                            </th>
                        </tr>
                        <template v-for="item in counterparties">
                            <tr
                                v-if="item.buyer"
                                @click="selectCounterpartie(item.id)"
                            >
                                <td>{{ item.name }}</td>
                            </tr>
                        </template>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label class="labl">Дело №</label>
                <input type="number" class="inp" v-model.number="app_id" />
            </div>
            <div class="form-group">
                <label class="labl">Комментарий</label>
                <div
                    id="comment"
                    class="inp-txt"
                    @keyup="commentEdit()"
                    contenteditable
                ></div>
            </div>
            <div class="nav-link" @click="addIncome()">Добавить</div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["inventory_items", "counterparties", "warehouses"],

    data() {
        return {
            showIncome: false,
            items_show: false,
            inventoryItems: [],
            inventory_item_id: "",
            warehouse_id: "",
            count: "",
            app_id: "",
            user_id: "",
            counterpartie_id: "",
            comment: "",
            created_at: "",
            warehouses_show: false,
            counterpartie_show: false,
            app_id: "",
            comment: "",
            created_at: "",
        };
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
        selectWarehouse(id) {
            this.warehouse_id = id;
            this.warehouses_show = false;
        },
        selectCounterpartie(id) {
            this.counterpartie_id = id;
            this.counterpartie_show = false;
        },
        selectItem(id) {
            this.inventory_item_id = id;
            this.items_show = false;
        },
        selectWarehouse(id) {
            this.warehouse_id = id;
            this.warehouses_show = false;
            axios.get(`/api/inventory_items/get/${id}`).then((res) => {
                this.inventoryItems = res.data;
                console.log(this.inventoryItems);
            });
        },
        selectCounterpartie(id) {
            this.counterpartie_id = id;
            this.counterpartie_show = false;
        },
        commentEdit() {
            this.comment = document.getElementById("comment").innerText;
        },
        tableSearch(k, table) {
            var phrase = document.getElementById(table + k);
            // console.log(phrase);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, "i");
            var flag = false;
            for (var i = 2; i < table.rows.length; i++) {
                flag = false;
                flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
                if (flag) {
                    table.rows[i].style.display = "";
                } else {
                    table.rows[i].style.display = "none";
                }
            }
        },
        addIncome() {
            let formData = new FormData();
            formData.append("created_at", this.created_at);
            formData.append("inventory_item_id", this.inventory_item_id);
            formData.append("warehouse_id", this.warehouse_id);
            formData.append("count", -this.count);
            formData.append("app_id", this.app_id);
            formData.append("user_id", this.user_id);
            formData.append("counterpartie_id", this.counterpartie_id);
            formData.append("comment", this.comment);

            axios
                .post(`/api/inventory_items/addIncome`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((res) => {
                    location.reload();
                });
        },
    },
};
</script>
