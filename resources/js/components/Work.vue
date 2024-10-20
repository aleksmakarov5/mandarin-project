
<template>
    <div>
     <button class="accordion-est" @click="show=!show">
            Работы по калькуляции
        </button>
<div  :class="show?'':'d_none'">
    <a :href="'/Work/'+app_id +'/doc_load'" class="nav-link">Загрузить данные из калькуляции</a>
    <table class="editable" >
        <thead>

           <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>

                        
                        <th scope="col">Цена</th>
                        <th scope="col">Нормочасы</th>
                        <th scope="col">Часы ПЗН</th>
                        <th scope="col">Сумма страховой</th>

                        <th scope="col">Сумма ПЗН</th>
                        

                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        <th scope="col"></th><th scope="col"></th><th scope="col"></th>
                        <th scope="col">{{ makeMoney(summ) }}</th>
                        <th scope="col">{{ makeMoney(summ_pzn) }}</th>
                    </tr>
                

        </thead>
        <tbody>
<template v-for="(work,index) in works">
<tr :class="isEdit(work.id)?'d_none':''">
    <td scope="col">
        {{ index+1 }}
    </td>
    <td @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ work.name }}</td>
        <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ makeMoney(work.price) }}</th>
  <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ makeCount(work.count_hours) }}</th>
    <td @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)">
        {{ makeCount(work.count_hours_pzn&&work.count_hours_pzn!=0?work.count_hours_pzn:work.count_hours) }}</td>
        <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ makeMoney(work.summ) }}</th>
        <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)">
        {{
            makeMoney((work.count_hours_pzn&&work.count_hours_pzn!=0 ? work.count_hours_pzn : work.count_hours) * work.price) }}
        </th>
    
    <td scope="col"><a @click.prevent="deletework(work.id)">
        <img src="/storage/img/delete.png" width="30" alt="delete"></a>
    </td>
</tr>
                   <tr :class="isEdit(work.id)?'':'d_none'">
                    <td scope="col">
                        {{ index+1 }}
                    </td>
                    <td><textarea id="name" v-model="name" class="w-100"   @blur="updateWork(work.id)" 
                        @keydown.enter="updateWork(work.id)" ></textarea></td>
                         <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ work.price }}</th>
  <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ work.count_hours }}</th>
                    <td><input type="text" id="count_hours_pzn" v-model="count_hours_pzn" class="w-100" @blur="updateWork(work.id)" 
                        @keydown.enter="updateWork(work.id)"></td>
                    
                    <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)" >
        {{ work.summ }}</th>
        <th @click="changeEditWorkId(work.id, work.name,  work.count_hours_pzn)">
        {{ 
            makeMoney((work.count_hours_pzn ? work.count_hours_pzn : work.count_hours) * work.price)  }}
    </th>
        
                </tr>
</template>
<tr>
                    <td scope="col">
                    </td>
                    <td><textarea  id="name" v-model="name_Cr" class="w-100"></textarea></td>
                    <td><input type="text" id="price" v-model="price_Cr" class="w-100"></td>
                    <td><input type="text" id="count_hours" v-model="count_hours_Cr" class="w-100"></td>
                    <td scope="col"><a  @click.prevent="createWork()">

                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
                    <th></th>
                    <th></th>
                </tr>
        </tbody>
    </table>
 </div>
</div>
</template>

<script>

	export default{
        props:[
            'app_id'
        ],

        data(){
            
            return{
                works:[],
                id_app:this.app_id,
                EditWorkId:null,
                name: '',
                price: '',
                count_hours_pzn: '',
                summ:'',
                name_Cr: '',
                price_Cr: '',
                count_hours_Cr: '',
                summ_pzn:0,
                show:true
            }
        },
	   
        mounted(){
            this.getWorks()
            
        },
    methods:{
        getWorks(){
            axios.get(`/api/work/${this.id_app}`)
            .then(res => {
                this.works = res.data[0]
                this.summ = res.data[1]
                this.summ_pzn = res.data[2]
                console.log(res)
            })

        },
        deletework(id) {
            if (confirm('Точно удалить?')) 
            {
axios.delete(`/api/work/${id}`)
                .then(res => {
                    this.getWorks()
                })
            }
            
        },
        changeEditWorkId(id,name,count_hours_pzn) {
            this.EditWorkId=id
            this.name=name
            this.count_hours_pzn=count_hours_pzn
        },
        isEdit(id){
            return this.EditWorkId===id
        },
        updateWork(id)
        {
            axios.post(`/api/work/${id}/update`, {
                id_application: this.app_id,
                name: this.name,
                count_hours_pzn: this.count_hours_pzn,
                
            })
           .then(res => {
            this.getWorks() 
           })
           this.changeEditWorkId(null)
            
        },
        createWork()
        {
             axios.post(`/api/work/create`, {
                 id_application: this.app_id,
                 name: this.name_Cr,
                 price: this.price_Cr,
                 count_hours: this.count_hours_Cr,
                summ: this.makeCount(this.price_Cr)*this.makeCount(this.count_hours_Cr)
            })
           .then(res => {
            this.getWorks()
           })
            this.name_Cr = ''
            this.price_Cr = ''
            this.count_hours_Cr = ''
        },
         makeMoney(n) {
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")+' р.';
         },
        makeCount(n) {
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
        },
        


        
        
    }
       
	}
    
</script>