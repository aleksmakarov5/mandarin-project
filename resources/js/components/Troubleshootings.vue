<template>
<div align="center">
    <table class="troubleshooting">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Наименование детали автомобиля</th>
                <th scope="col">Повреждение</th>

                <th scope="col">Требуемое ремонтное воздействие</th>
                <th scope="col"></th>
            </tr>

        </thead>
        <tbody>


<template v-for="(troubleshooting,index) in troubleshootings">
                <tr  @click="changeEditTroubleshootingId(troubleshooting.id,troubleshooting.PartName,troubleshooting.Work,troubleshooting.Сonclusion)" >
                    <td scope="col">
                        {{ index+1 }}
                    </td>
                    <td :id="'Part_Name'+troubleshooting.id" contenteditable @blur="editPartName(troubleshooting.id)">
                        {{ troubleshooting.PartName }}</td>
                        <td  :id="'Сonclusion_'+troubleshooting.id" contenteditable @blur="editСonclusion(troubleshooting.id)">   
                        {{ troubleshooting.Сonclusion }}</td>
                    <td  :id="'Work_'+troubleshooting.id" contenteditable @blur="editWork(troubleshooting.id)">
                        {{ troubleshooting.Work }}</td>
                   
                    
                    <td scope="col" @click.prevent="deletetroubleshooting(troubleshooting.id)">
                        <img src="/storage/img/delete.png" width="30" alt="delete">
                    </td>
                </tr>
              
            </template>
              <tr>
                    <td scope="col">
                    </td>
                    <td><textarea cols="400" id="PartName" v-model="PartName_Cr" class="w-100"></textarea></td>
                    <td><textarea cols="400" id="Сonclusion" v-model="Сonclusion_Cr" class="w-100"></textarea></td>
                    <td><textarea cols="400" id="Work" v-model="Work_Cr" class="w-100"></textarea></td>
                    <td scope="col"><a  @click.prevent="createTroubleshooting()">
                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
                </tr>
        </tbody>
    </table>
</div>
</template>

<script>

	export default{
        props:[
            'app_id'
        ],

        data(){
            
            return{
                troubleshootings:[],
                id_app:this.app_id,
                EditTroubleshootingId:null,
                PartName: '',
                Work: '',
                Сonclusion: '',
                PartName_Cr: '',
                Work_Cr: '',
                Сonclusion_Cr: '',
            }
        },
	   
        mounted(){
            this.getTroubleshutings()
            
        },
    methods:{
        getTroubleshutings(){
            axios.get(`/api/troubleshootings/${this.id_app}`)
            .then(res => {
                this.troubleshootings=res.data
                console.log(res)
            })

        },
        deletetroubleshooting(id) {
            if (confirm('Точно удалить?')) 
            {
axios.delete(`/api/troubleshootings/${id}`)
                .then(res => {
                    this.getTroubleshutings()
                })
            }
            
        },
        changeEditTroubleshootingId(id,PartName,Work,Сonclusion) {
            this.PartName=PartName
            this.Work=Work
            this.Сonclusion=Сonclusion
        },
        isEdit(id){
            return this.EditTroubleshootingId===id
        },
        updateTroubleshooting(id)
        {
            axios.post(`/api/troubleshootings/${id}/update`, {
                id_application: this.app_id, PartName: this.PartName, Work: this.Work,  Сonclusion: this.Сonclusion
            })
           .then(res => {
            this.getTroubleshutings() 
           })
           this.changeEditTroubleshootingId(null)
            
        },
        createTroubleshooting()
        {
             axios.post(`/api/troubleshootings/create`, {
                id_application: this.app_id, PartName: this.PartName_Cr, Work: this.Work_Cr,  Сonclusion: this.Сonclusion_Cr
            })
           .then(res => {
            this.getTroubleshutings()
           })
            this.PartName_Cr = ''
            this.Work_Cr = ''
            this.Сonclusion_Cr = ''
        },

        editPartName(id)
        {
           let part_name = document.getElementById('Part_Name'+id);
            this.PartName = part_name.innerHTML;
            this.updateTroubleshooting(id)
        },
        editСonclusion(id)
        {
           let сonclusion = document.getElementById('Сonclusion_'+id);
            this.Сonclusion = сonclusion.innerHTML;
            this.updateTroubleshooting(id)
        },
        editWork(id)
        {
            let work = document.getElementById('Work_'+id);
            this.Work = work.innerHTML;
            this.updateTroubleshooting(id)
        }


        
        
    }
       
	}
    
</script>