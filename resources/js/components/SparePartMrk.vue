
<template>
    <div>
     <button class="accordion-est" @click="show=!show">
            Запчасти
        </button>
<div  :class="show?'':'d_none'">
    <a :href="'/SparePart/'+app_id +'/doc_load'" class="nav-link">Загрузить данные из калькуляции</a>
    <table class="editable" >
        <thead>

           <tr>
                        <th scope="col">#</th>
                    <th scope="col">Наименование</th>
                    <th scope="col">Кат. номер</th>
                    <th scope="col">Зак. номер</th>

                        <th scope="col">Цена СК</th>
                        <th scope="col">Цена ПЗН</th>

                        <th scope="col">Оригинал</th>
                        <th scope="col">Аналог</th>
                        <th scope="col">Б/у</th>

                        <th scope="col">Разница </th>




                    <th scope="col">Комментарий</th>
                    <template v-if="rout_it=='directions'">
                        <th scope="col">Статус</th>
                        <th scope="col">Дата заказа</th>
                        <th scope="col">Ожидаемая дата</th>
                    </template>

                            <th scope="col"></th>
                    </tr>
                   <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">{{ makeMoney(summ_ic_full) }}</th>
                        <th scope="col">{{ makeMoney(summ_sto) }}</th>
                        <th scope="col" colspan="3"></th>
                        <th scope="col">

                                <div :class="classDanger(delta_res_full)"> {{ makeMoney(delta_res_full) }}</div>


                        </th>

                        <th scope="col" colspan=2></th>
                    <template v-if="rout_it=='directions'">
                            <th scope="col"></th>


                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                    </template>

                    </tr>


        </thead>
        <tbody>
<template v-for="(sparepart,index) in spareparts">
<tr :class="isAuthor(sparepart.author)"
@click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.comment,sparepart.price_sto)" >
    <td scope="col">
        {{ index+1 }}
    </td>
    <td :id="'name_'+sparepart.id" contenteditable @blur="editName(sparepart.id,index)" @keydown.enter="editName(sparepart.id,index)">
        {{ sparepart.name }}</td>
    <td >
        {{ sparepart.part_number }}</td>
    <td >
        {{ sparepart.order_number }}</td>
        <td  class="ic_price">
        {{ makeCount(sparepart.price_ic_full) }}</td>
        <td  class="ic_price" :id="'price_sto_'+sparepart.id" contenteditable @blur="editPriceSto(sparepart.id,index)" @keydown.enter="editPriceSto(sparepart.id,index)">
        {{makeCount(sparepart.price_sto&&sparepart.price_sto!=0?sparepart.price_sto:sparepart.price_ic_full) }}</td>
        <td :class="classSelect(1,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,1,sparepart.name,index)">
        {{ makeCount(sparepart.price_original) }}</td>
<td  :class="classSelect(2,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,2,sparepart.name,index)">
        {{ makeCount(sparepart.price_analogue) }}</td>
        <td  :class="classSelect(3,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,3,sparepart.name,index)">
        {{ makeCount(sparepart.price_used) }}</td>
        <th >
            <div :class="classDanger(sparepart.delta)">
        {{
            makeCount(sparepart.delta) }}</div></th>

        <td :id="'comment_'+sparepart.id" contenteditable @blur="editComment(sparepart.id,index)" @keydown.enter="editComment(sparepart.id,index)">
        {{ sparepart.comment }}</td>

                    <template v-if="rout_it=='directions'">
        <td  :class="sparePartStatus(sparepart.status_id)">
        {{ sparepart.StatusName }}</td>
         <td > {{fortmatDate(sparepart.date_price)}}
        </td>
        <td >
            {{fortmatDate(sparepart.date_wait)}}
        </td>
        </template>
    <td scope="col"><a @click.prevent="deletesparepart(sparepart.id)" >
        <img src="/storage/img/delete.png" width="20" alt="delete"></a>
    </td>
</tr>

</template>
<tr>
                    <td scope="col"><a  @click.prevent="createSparePart()">

                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
                    <td><input type="text"  id="name" v-model="name_Cr" class="w-100" @keydown.enter="createSparePart()"></td>


                </tr>
        </tbody>
    </table>
 </div>
</div>
</template>

<script>

	export default{
        props:[
            'app_id',
            'rout',
            'in_statuses',
            'in_spareparts',
            'in_summ_sto',
            'in_summ_ic_full',
            'in_delta_res_full',
        ],

        data(){

            return{
                spareparts:this.in_spareparts,
                id_app: this.app_id,
                rout_it:this.rout,
                EditSparePartId:null,
                name: '',
                part_number: '',
                order_number: '',
                price_ic_full:'',
                price_original:'',
                price_analogue:'',
                price_used: '',
                price_sto:'',
                date_price: '',
                statusname: '',
                date_wait: '',
                status_id: '',
                comment: '',
                summ_sto:this.in_summ_sto,
                delta_res_full: this.in_delta_res_full,
                delta: 0,
                selected_price: '',
                selected_mrk:'',
                statuses: this.in_statuses,
                summ_ic_full:this.in_summ_ic_full,
                name_Cr: '',
                show:true
            }
        },

        mounted(){
            // this.getSpareParts()

        },
    methods:{

        getSpareParts(res){

                this.spareparts = res.data[0]
                this.summ_ic_full = res.data[1]
                this.summ_sto = res.data[2]
                this.delta_res_full = res.data[3]


        },
        deletesparepart(id) {
            if (confirm('Точно удалить?'))
            {
        axios.delete(`/api/sparepart/${id}`)
                .then(res => {
                    this.getSpareParts(res)
                })
            }

        },
        changeEditSparePartId(id,name,comment,price_sto) {
            this.name=name
            this.comment=comment
            this.price_sto = price_sto
        },
        isEdit(id){
            return this.EditSparePartId===id
        },
        updateSparePart(id,index)
        {
            axios.post(`/api/sparepart/${id}/update`, {
                id_application: this.app_id,
                name: this.name,
                comment: this.comment,
                price_sto: this.price_sto

            })
                .then(res => {
                    this.getSpareParts(res)
           })


        //    this.changeEditSparePartId(null)

        },
        updateSparePartMrk(id, select,name,index)
        { axios.post(`/api/sparepart/${id}/update`, {
                id_application: this.app_id,
                name: name,
                selected_mrk: select
            })
            .then(res => {
               this.getSpareParts(res)
            // this.getSpareParts()
           })



        },

        createSparePart()
        {
             axios.post(`/api/sparepart/create`, {
                 id_application: this.app_id,
                 name: this.name_Cr,
                 author: 5
            })
           .then(res => {
            this.getSpareParts(res)
           })
            this.name_Cr = ''
        },
         makeMoney(n) {
            if (!n) n = 0;
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")+' р.';
         },
        makeCount(n) {
            if (!n) n = 0;
            return parseFloat(n).toFixed(0).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
        },
        classDanger(n) {
            if (n < 0) return 'alert alert-success';
            else
            return 'alert alert-danger';
        },
        classSelect(n, mrk, zap) {
            if (mrk == n && zap == n) return 'zapmrk';
            else
                if (zap == n) return 'zap';
                else
                    if (mrk == n) return 'mrk';
                    else
                        return '';
        },
         editName(id,index)
        {
           let name = document.getElementById('name_'+id);
            this.name = name.innerText;
             this.updateSparePart(id,index)
        },
        editPriceSto(id,index)
        {
            let price_sto = document.getElementById('price_sto_'+id);
            this.price_sto = price_sto.innerText;
            this.updateSparePart(id,index)
        },
        editComment(id,index)
        {
            let comment = document.getElementById('comment_'+id);
            this.comment = comment.innerText;
            this.updateSparePart(id,index)
        },
        isAuthor(author) {
            if (author==5) return "pink";
        },
        sparePartStatus(status) {

                if (status == 1)
                    return 'yellow';
                else
                    if (status == 2)
                        return 'green';
                    else
                        if (status == 3)
                        return 'teal';
                        else
                            if (status == 4)
                                return 'blue';
                            else
                return 'gray';
            },
        fortmatDate(date)
        {
            let options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            };
            if(date)
                return new Date(date).toLocaleString('ru', options);
        else return ''
        }

    }

	}

</script>
