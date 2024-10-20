
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
<tr :class="isEdit(sparepart.id)?'d_none':''">
    <td scope="col">
        {{ index+1 }}
    </td>
    <td @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.comment,sparepart.price_sto)" >
        {{ sparepart.name }}</td>
    <td >
        {{ sparepart.part_number }}</td>  
    <td >
        {{ sparepart.order_number }}</td> 
        <td  class="ic_price">
        {{ makeCount(sparepart.price_ic_full) }}</td>
        <td  class="ic_price" @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.comment,sparepart.price_sto)">
        {{makeCount(sparepart.price_sto&&sparepart.price_sto!=0?sparepart.price_sto:sparepart.price_ic_full) }}</td>
        <td :class="classSelect(1,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,1,sparepart.name)">
        {{ makeCount(sparepart.price_original) }}</td>
<td  :class="classSelect(2,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,2,sparepart.name)">
        {{ makeCount(sparepart.price_analogue) }}</td>
        <td  :class="classSelect(3,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,3,sparepart.name)">
        {{ makeCount(sparepart.price_used) }}</td>
        <th >
            <div :class="classDanger(sparepart.delta)">
        {{
            makeCount(sparepart.delta) }}</div></th>

        <td  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.comment,sparepart.price_sto)">
        {{ sparepart.comment }}</td>

                    <template v-if="rout_it=='directions'">
        <td >
        {{ sparepart.statusname }}</td>
        <td >
        {{ sparepart.date_price }}</td>
        <td >
        {{ sparepart.date_wait }}</td>
        </template>
    <td scope="col"><a @click.prevent="deletesparepart(sparepart.id)">
        <img src="/storage/img/delete.png" width="20" alt="delete"></a>
    </td>
</tr>
                   <tr :class="isEdit(sparepart.id)?'':'d_none'">
                    <td scope="col">
        {{ index+1 }}
    </td>
                    <td>
        <input type="text" id="name" v-model="name" class="w-100" @blur="updateSparePart(sparepart.id)" 
                        @keydown.enter="updateSparePart(sparepart.id)"></td>
    <td >
        {{ sparepart.part_number }}</td>  
    <td >
        {{ sparepart.order_number }}</td> 
        <td  class="ic_price">
        {{ sparepart.price_ic_full }}</td> 
        <td>
        <input type="text" id="name" v-model="price_sto" class="w-100" @blur="updateSparePart(sparepart.id)" 
                        @keydown.enter="updateSparePart(sparepart.id)"></td>
        <td  :class="classSelect(1,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,1,sparepart.name)">
        {{ sparepart.price_original }}</td>
<td  :class="classSelect(2,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,2,sparepart.name)">
        {{ sparepart.price_analogue }}</td>
        <td  :class="classSelect(3,sparepart.selected_mrk,sparepart.selected_price)" @contextmenu.prevent="updateSparePartMrk(sparepart.id,3,sparepart.name)">
        {{ sparepart.price_used }}</td>
        <th >
        {{ sparepart.delta }}</th>

        <td >
        <textarea id="comment" v-model="comment" class="w-100"   @blur="updateSparePart(sparepart.id)" 
                        @keydown.enter="updateSparePart(sparepart.id)" ></textarea></td>
                         <template v-if="rout_it=='directions'">
        <td >

                   
        {{ sparepart.statusname }}</td>
        <td >
        {{ sparepart.date_price }}</td>
        <td >
        {{ sparepart.date_wait }}</td>
        </template>
        
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
            this.EditSparePartId=id
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
        }
    }
       
	}
    
</script>