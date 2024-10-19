
<template>
    <div>
     <button class="accordion-est" @click="show=!show">
            Фактические работы
        </button>
<div  :class="show?'':'d_none'">
    <table class="troubleshooting" >
        <thead>

           <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>

                        
                        <th scope="col">Цена сервиса</th>
                        

                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        <th scope="col">{{ makeMoney(summ_service) }}</th>
                        <th scope="col">
                           

                                    
                            
                        </th>

                    </tr>
                

        </thead>
        <tbody>
<template v-for="(work,index) in works">
<tr :class="isEdit(work.id)?'d_none':''">
    <td scope="col">
        {{ index+1 }}
    </td>
    <td @click="changeEditWorkId(work.id, work.name,  work.price_service)" @contextmenu.prevent="deletework(work.id)">
        {{ work.name }}</td>
  
    <td @click="changeEditWorkId(work.id, work.name,  work.price_service)">
        {{ makeMoney(work.price_service) }}</td>
    
    <td scope="col" @click.prevent="deleteWork(work.id)">
        <img src="/storage/img/delete.png" width="20" alt="delete">
    </td>
</tr>
                   <tr :class="isEdit(work.id)?'':'d_none'">
                    <td scope="col">
                        {{ index+1 }}
                    </td>
                    <td><textarea id="name" v-model="name" class="w-100"  cols=400 @blur="updateWork(work.id)" 
                        @keydown.enter="updateWork(work.id)" ></textarea></td>
                    <td><input type="text" id="price_service" v-model="price_service" class="w-100" @blur="updateWork(work.id)" 
                        @keydown.enter="updateWork(work.id)"></td>
                    <td scope="col"> <a @click.prevent="deletework(work.id)">
                        <img src="/storage/img/delete.png" width="15" alt="delete"></a>
                    </td>
                </tr>
</template>
 <tr>
                    <td scope="col">
                    </td>
                    <td><textarea cols="400" id="name" v-model="name_Cr" class="w-100"></textarea></td>
                    <td><input type="text" id="price_service" v-model="price_service_Cr" class="w-100"></td>
                    <td scope="col"><a  @click.prevent="createWork()">
                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
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
                price_service: '',
                name_Cr: '',
                price_service_Cr: '',
                summ_service:'',
                show:true
            }
        },
	   
        mounted(){
            this.getWorks()
            
        },
    methods:{
        getWorks(){
            axios.get(`/api/factwork/${this.id_app}`)
            .then(res => {
                this.works = res.data[0]
                this.summ_service=res.data[1]
                console.log(res)
            })

        },
        deletework(id) {
            if (confirm('Точно удалить?')) 
            {
axios.delete(`/api/factwork/${id}`)
                .then(res => {
                    this.getWorks()
                })
            }
            
        },
        changeEditWorkId(id,name,price_service) {
            this.EditWorkId=id
            this.name=name
            this.price_service=price_service
        },
        isEdit(id){
            return this.EditWorkId===id
        },
        updateWork(id)
        {
            axios.post(`/api/factwork/${id}/update`, {
                id_application: this.app_id,
                name: this.name,
                price_service: this.price_service,
                summ: 0
            })
           .then(res => {
            this.getWorks() 
           })
           this.changeEditWorkId(null)
            
        },
        createWork()
        {
             axios.post(`/api/factwork/create`, {
                 id_application: this.app_id,
                 name: this.name_Cr,
                 price_service: this.price_service_Cr,
                summ: 0
            })
           .then(res => {
            this.getWorks()
           })
            this.name_Cr = ''
            this.price_service_Cr = ''
        },
         makeMoney(n) {
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")+' р.';
        }

        
        
    }
       
	}
    
</script>