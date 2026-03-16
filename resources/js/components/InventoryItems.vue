<style>

</style>

<template>
    <a class="nav-link" @click.prevent="showFace = !showFace">Номенклатура</a>
    <div v-if="showFace" class="back-face" @click="showFace = !showFace"></div>
    <div  v-if="showFace" class="face">
        <table class="font-table">
            <tr>
                <th>Кат. номер</th>
                <th>Название</th>
                <th>Авто</th>
                <th>Комментарий</th>
            </tr>
            <tr v-for="item in inventoryItems">
                <td>{{ item.part_number }}</td>
                <td>{{ item.part_name }}</td>
                <td>{{ item.car_model }}</td>
                <td>{{item.description}}</td>
            </tr>
            <tr>
                <th><input type="text" placeholder="Кат. номер" v-model="part_number"/></th>
                <th><input type="text" placeholder="Название" v-model="part_name"/></th>
                <th><input type="text" placeholder="Авто" v-model="car_model"></th>
                <th><input type="text" placeholder="Комментарий" v-model="description"></th>
                <th>
                    <input class="nav-link" type="button" value="Добавить" @click="addItem()"></input>
                </th>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    props: ["inventory_items", "counterparties"],

    data() {
        return {
            showFace: false,
            inventoryItems: this.inventory_items,
            part_name: '',
            part_number: '',
            car_model: '',
            description: '',
        };
    },
    mounted() {},
    methods: {
        addItem()
        {
let formData = new FormData();
            formData.append('part_name', this.part_name);
            formData.append('part_number', this.part_number);
            formData.append('car_model', this.car_model);
            formData.append('description', this.description);
            axios.post(`/api/inventory_items/addItem`, formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }
            )
                .then(res => {
                    // console.log(res.data);
                     this.part_name = '';
                    this.part_number = '';
                    this.car_model = '';
                    this.description = '';
                    this.inventoryItems.unshift(res.data)
                })
        },
    },
};
</script>
