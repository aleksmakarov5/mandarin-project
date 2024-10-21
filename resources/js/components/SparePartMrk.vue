
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
<tr :class=isAuthor(sparepart.author)
@click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.comment,sparepart.price_sto)" >
    <td scope="col">
        {{ index+1 }}
    </td>
    <td :id="'name_'+sparepart.id" contenteditable @blur="editName(sparepart.id)" @keydown.enter="editName(sparepart.id)">
        {{ sparepart.name }}</td>
    <td >
        {{ sparepart.part_number }}</td>  
    <td >
        {{ sparepart.order_number }}</td> 
        <td  class="ic_price">
        {{ makeCount(sparepart.price_ic_full) }}</td>
        <td  class="ic_price" :id="'price_sto_'+sparepart.id" contenteditable @blur="editPriceSto(sparepart.id)" @keydown.enter="editPriceSto(sparepart.id)">
        {{makeCount(sparepart.price_sto&&sparepart.price_sto!=0?sparepart.price_sto:sparepart.price_ic_full) }}</td>
        <td :class="classSelect(1,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,1,sparepart.name)">
        {{ makeCount(sparepart.price_original) }}</td>
<td  :class="classSelect(2,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,2,sparepart.name)">
        {{ makeCount(sparepart.price_analogue) }}</td>
        <td  :class="classSelect(3,sparepart.selected_mrk,sparepart.selected_price)" @dblclick.prevent="updateSparePartMrk(sparepart.id,3,sparepart.name)">
        {{ makeCount(sparepart.price_used) }}</td>
        <th >
            <div :class="classDanger(sparepart.delta)">
        {{
            makeCount(sparepart.delta) }}</div></th>

        <td :id="'comment_'+sparepart.id" contenteditable @blur="editComment(sparepart.id)" @keydown.enter="editComment(sparepart.id)">
        {{ sparepart.comment }}</td>

                    <template v-if="rout_it=='directions'">
        <td  :class="sparePartStatus(sparepart.status_id)">
        {{ sparepart.StatusName }}</td>
        <td >
        {{ sparepart.date_price }}</td>
        <td >
        {{ sparepart.date_wait }}</td>
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
            'rout'
        ],

        data(){
            
            return{
                spareparts:[],
                id_app: this.app_id,
                rout_it:this.rout,
                EditSparePartId:null,
                name: '',
                part_number: '',
                order_number: '',
                price_analogue:'',
                price_used: '',
                price_sto:'',
                date_price: '',
                statusname: '',
                date_wait: '',
                comment: '',
                summ_sto:0,
                delta_res_full: 0,
                delta: 0,
                selected_price: '',
                selected_mrk:'',
                
                summ_ic_full:0,
                name_Cr: '',    
                show:true
            }
        },
	   
        mounted(){
            this.getSpareParts()
            
        },
    methods:{
        getSpareParts(){
            axios.get(`/api/sparepart/${this.id_app}`)
            .then(res => {
                this.spareparts = res.data[0]
                this.summ_ic_full = res.data[1]
                this.summ_sto = res.data[2]
                this.delta_res_full = res.data[3]
                console.log(res)
            })

        },
        deletesparepart(id) {
            if (confirm('Точно удалить?')) 
            {
        axios.delete(`/api/sparepart/${id}`)
                .then(res => {
                    this.getSpareParts()
                })
            }
            
        },
        changeEditSparePartId(id,name,comment,price_sto) {
            this.name=name
            this.comment=comment
            this.price_sto = price_sto
            console.log(this)
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
                price_sto: this.price_sto
                
            })
           .then(res => {
            this.getSpareParts() 
           })
           console.log(this)
           this.changeEditSparePartId(null)
            
        },
        updateSparePartMrk(id, select,name)
        { axios.post(`/api/sparepart/${id}/update`, {
                id_application: this.app_id,
                name: name,
                selected_mrk: select 
            })
           .then(res => {
            this.getSpareParts() 
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
            this.getSpareParts()
           })
            this.name_Cr = ''
        },
         makeMoney(n) {
            if (!n) n = 0;
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")+' р.';
         },
        makeCount(n) {
            if (!n) n = 0;
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
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
         editName(id)
        {
           let name = document.getElementById('name_'+id);
            this.name = name.innerHTML;
            this.updateSparePart(id)
        },
        editPriceSto(id)
        {
            let price_sto = document.getElementById('price_sto_'+id);
            this.price_sto = price_sto.innerHTML;
            this.updateSparePart(id)
        },
        editComment(id)
        {
            let comment = document.getElementById('comment_'+id);
            this.comment = comment.innerHTML;
            this.updateSparePart(id)
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
            }

    }
       
	}
    
</script>