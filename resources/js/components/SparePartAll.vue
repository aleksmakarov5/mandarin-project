
<template>
    <div>
     <button class="accordion-est" @click="show=!show">
            Запчасти
        </button>
<div  :class="show?'':'d_none'">
    <a :href="'/SparePart/'+app_id +'/doc_load'" class="nav-link">Загрузить данные из калькуляции</a>
    <table class="editable"  @blur="changeEditSparePartId(null)">
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
<tr :class="isAuthor(sparepart.author)"  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.part_number,sparepart.order_number,sparepart.price_ic_full,sparepart.price_sto,sparepart.price_original,sparepart.price_analogue,sparepart.price_used,sparepart.comment,sparepart.status_id,sparepart.date_price,sparepart.date_wait)">
    <td scope="col">
        {{ index+1 }}
    </td>
    <td  :id="'name_'+sparepart.id" contenteditable @blur="editName(sparepart.id)" @keydown.enter="editName(sparepart.id)">
        {{ sparepart.name }}</td>
    <td>
          <!-- :id="'part_number_'+sparepart.id" contenteditable @blur="editPartNumber(sparepart.id)" @keydown.enter="editPartNumber(sparepart.id)"> -->
        {{ sparepart.part_number }}</td>
    <td  :id="'order_number_'+sparepart.id" contenteditable @blur="editOrderNumber(sparepart.id)" @keydown.enter="editOrderNumber(sparepart.id)">
        {{ sparepart.order_number }}</td>
        <td  class="ic_price"  :id="'price_ic_full_'+sparepart.id" contenteditable @blur="editPriceIcFull(sparepart.id)" @keydown.enter="editPriceIcFull(sparepart.id)">
        {{ makeCount(sparepart.price_ic_full) }}</td>
        <td  class="ic_price"  :id="'price_sto_'+sparepart.id" contenteditable @blur="editPriceSto(sparepart.id)" @keydown.enter="editPriceSto(sparepart.id)">
        {{makeCount(sparepart.price_sto&&sparepart.price_sto!=0?sparepart.price_sto:sparepart.price_ic_full) }}</td>
        <td :class="classSelect(1,sparepart.selected_mrk,sparepart.selected_price)"  :id="'price_original_'+sparepart.id" contenteditable @blur="editPriceOriginal(sparepart.id)" @keydown.enter="editPriceOriginal(sparepart.id)">
        {{ makeCount(sparepart.price_original) }}</td>
<td  :class="classSelect(2,sparepart.selected_mrk,sparepart.selected_price)"   :id="'price_analogue_'+sparepart.id" contenteditable @blur="editPriceAnalogue(sparepart.id)" @keydown.enter="editPriceAnalogue(sparepart.id)">
        {{ makeCount(sparepart.price_analogue) }}</td>
        <td  :class="classSelect(3,sparepart.selected_mrk,sparepart.selected_price)":id="'price_used_'+sparepart.id" contenteditable @blur="editPriceUsed(sparepart.id)" @keydown.enter="editPriceUsed(sparepart.id)" >
        {{ makeCount(sparepart.price_used) }}</td>
        <th >
            <div :class="classDanger(sparepart.delta)">
        {{
            makeCount(sparepart.delta) }}</div></th>

        <td  :id="'comment_'+sparepart.id" contenteditable @blur="editComment(sparepart.id)" @keydown.enter="editComment(sparepart.id)">
        {{ sparepart.comment }}</td>

                    <template v-if="rout_it=='directions'">
                        <template v-if="isEdit(sparepart.id)">


        <td :class="sparePartStatus(sparepart.status_id)">
<select class=sel id="status_id" name="status_id" v-model="status_id" @change="updateSparePart(sparepart.id)">
    <option value=0 >------</option>
    <template v-for="status in statuses">
                                <option :value="status.id" >{{status.StatusName}}</option>
                                </template>
                            </select>

        </td>
        <td > <input type="date" name="date_price" id="date_price" v-model="date_price" @blur="updateSparePart(sparepart.id)" @keydown.enter="updateSparePart(sparepart.id)">
        </td>
        <td >
            <input type="date" name="date_wait" id="date_wait" v-model="date_wait" @blur="updateSparePart(sparepart.id)" @keydown.enter="updateSparePart(sparepart.id)">
        </td>
        </template>
        <template v-else>
            <td  :class="sparePartStatus(sparepart.status_id)">
{{ sparepart.StatusName }}
        </td>
        <td > {{fortmatDate(sparepart.date_price)}}
        </td>
        <td >
            {{fortmatDate(sparepart.date_wait)}}
        </td>

        </template>
        </template>
    <td scope="col"><a @click.prevent="deletesparepart(sparepart.id)">
        <img src="/storage/img/delete.png" width="20" alt="delete"></a>
    </td>
</tr>
 </template>
<tr>
                    <td scope="col"><a  @click.prevent="createSparePart()">

                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
                    <td><input type="text"  id="name" v-model="name_Cr" class="w-100"></td>


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
                show: true,

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
                this.statuses=res.data[4]



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
        //  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.part_number,sparepart.order_number,sparepart.price_ic_full,sparepart.price_sto,sparepart.price_original,sparepart.price_analogue,sparepart.price_used)"
        changeEditSparePartId(id,name,part_number,order_number,price_ic_full,price_sto,price_original,price_analogue,price_used,comment,status_id,date_price,date_wait) {
            this.EditSparePartId=id
            this.name=name
            this.comment=comment
            this.price_sto = price_sto
            this.price_analogue = price_analogue
            this.price_used = price_used
            this.price_original = price_original
            this.part_number = part_number
            this.order_number = order_number
            this.price_ic_full = price_ic_full
            this.status_id = status_id
            this.date_price = date_price
            this.date_wait = date_wait


        },
        isEdit(id){
            return this.EditSparePartId===id
        },

        updateSparePart(id)
        {
            axios.post(`/api/sparepart/${id}/update`, {
                id_application: this.app_id,
                name: this.name,
                comment: this.comment,
                price_sto: this.price_sto,
                price_analogue: this.price_analogue,
                price_used: this.price_used,
                price_original: this.price_original,
                part_number: this.part_number,
                order_number: this.order_number,
                price_ic_full: this.price_ic_full,
                status_id: this.status_id,
                date_price: this.date_price,
                date_wait: this.date_wait

            })
           .then(res => {
                    this.getSpareParts(res)
           })
           this.changeEditSparePartId(null)


        },


        createSparePart()
        {
             axios.post(`/api/sparepart/create`, {
                 id_application: this.app_id,
                 name: this.name_Cr,
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
            if (!n) return '';
            if((n=='-')) return 'нет';
            let text = parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")
            text=text.substring(0, text.indexOf("."))
            if (text == '0')
                return ''
            else

            {
                return text;
            }

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
        isAuthor(author) {
            if (author == 5) return "pink";

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
        showTr(id, author)
        {
            if (this.isEdit(id))
                return 'd_none'
            else
            return this.isAuthor(author)

        },
            editName(id)
        {
           let name = document.getElementById('name_'+id);
                this.name = name.innerText;
                this.updateSparePart(id)
        },
        editComment(id)
        {
            let comment = document.getElementById('comment_'+id);
            this.comment = comment.innerText;
            this.updateSparePart(id)
        },
        editPriceUsed(id)
        {
            let price_used = document.getElementById('price_used_' + id);

            this.price_used = price_used.innerText;
            this.updateSparePart(id)
        },
        editPriceAnalogue(id)
        {
            let price_analogue = document.getElementById('price_analogue_'+id);
            this.price_analogue = price_analogue.innerText;
            this.updateSparePart(id)
        },
        editPriceOriginal(id)
        {
            let price_original = document.getElementById('price_original_'+id);
            this.price_original = price_original.innerText;
            this.updateSparePart(id)
        },
        editPriceICFull(id)
        {
            let price_ic_full = document.getElementById('price_ic_full_'+id);
            this.price_ic_full = price_ic_full.innerText;
            this.updateSparePart(id)
        },
        editPriceSto(id)
        {
            let price_sto = document.getElementById('price_sto_'+id);
            this.price_sto = price_sto.innerText;
            this.updateSparePart(id)
        },
        editPartNumber(id)
        {
            let part_number = document.getElementById('part_number_'+id);
            this.part_number = part_number.innerText;
            this.updateSparePart(id)
        },
        editOrderNumber(id)
        {
            let order_number = document.getElementById('order_number_'+id);
            this.order_number = order_number.innerText;
            this.updateSparePart(id)
        },
        editPriceIcFull(id)
        {
            let price_ic_full = document.getElementById('price_ic_full_'+id);
            this.price_ic_full = price_ic_full.innerText;
            this.updateSparePart(id)
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
