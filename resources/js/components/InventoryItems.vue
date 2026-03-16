<style>
.font-table {
    font-size: large;
    width: 100%;
    border-style: double;
}
.font-table td {
    border-style: double;
}
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
            </tr>
            <tr v-for="item in inventoryItems">
                <td>{{ item.part_number }}</td>
                <td>{{ item.part_name }}</td>
                <td>{{ item.car_model }}</td>
            </tr>
            <tr>
                <th><input type="text" placeholder="Кат. номер" v-model="part_number"/></th>
                <th><input type="text" placeholder="Название" v-model="part_name"/></th>
                <th><input type="text" placeholder="Авто" v-model="car_model"></th>
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
            car_model:'',
        };
    },
    mounted() {},
    methods: {
        addItem()
        {
let formData = new FormData();
            formData.append('name', this.name);
            formData.append('city', this.city);
            formData.append('company', this.company);
            formData.append('email', this.email);
            axios.post(`/api/mail/addUser`, formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }
            )
                .then(res => {
                    // console.log(res.data);
                    this.getBook();
                    this.name = '';
                    this.city = '';
                    this.company = '';
                    this.email = '';
                    this.inventoryItems.unshift(res.data)
                })
        },
    },
};
</script>
