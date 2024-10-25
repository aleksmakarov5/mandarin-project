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
                <tr  @click="changeEditTroubleshootingId(index)" >
                    <td scope="col">
                        {{ index+1 }}
                    </td>
                    <td :id="'Part_Name_'+troubleshooting.id" contenteditable  @blur="editPartName(troubleshooting.id)" @keydown.enter="editPartName(troubleshooting.id)">
                        {{ troubleshooting.PartName }}</td>
                    <td :id="'Сonclusion_'+troubleshooting.id" contenteditable  @blur="editСonclusion(troubleshooting.id)" @keydown.enter="editСonclusion(troubleshooting.id)">
                        {{ troubleshooting.Сonclusion }}</td>
                    <td  :id="'Work_'+troubleshooting.id" contenteditable  @blur="editWork(troubleshooting.id)" @keydown.enter="editWork(troubleshooting.id)">
                        {{ troubleshooting.Work }}</td>


                    <td scope="col" @click.prevent="deletetroubleshooting(troubleshooting.id)">
                        <img src="/storage/img/delete.png" width="30" alt="delete">
                    </td>
                </tr>

            </template>
              <tr>

                    <td scope="col" colspan="5" align="center"><a  @click.prevent="createTroubleshooting()">
                        <img src="/storage/img/add.png" width="30" alt="create"></a>
                    </td>
                </tr>
        </tbody>
    </table>
</div>
</template>

<script>

	export default{
        props:[
            'app_id',
            'in_troubleshootings'
        ],

        data(){

            return{
                troubleshootings: this.in_troubleshootings,
                index: 0,
                id_app:this.app_id,
                EditTroubleshootingId:null,
                PartName: '',
                Work: '',
                Сonclusion: '',
                PartName_Cr: ' ',
                Work_Cr: ' ',
                Сonclusion_Cr: ' ',
            }
        },

        mounted(){
            // this.getTroubleshutings()

        },
    methods:{
        getTroubleshutings(res){

                this.troubleshootings=res.data
                console.log(res)


        },
        deletetroubleshooting(id) {
            if (confirm('Точно удалить?'))
            {
axios.delete(`/api/troubleshootings/${id}`)
                .then(res => {
                    this.getTroubleshutings(res)
                })
            }

        },
        changeEditTroubleshootingId(index) {
            this.PartName=this.troubleshootings[index].PartName
            this.Work=this.troubleshootings[index].Work
            this.Сonclusion=this.troubleshootings[index].Сonclusion
        },
        isEdit(id){
            return this.EditTroubleshootingId===id
        },
        updateTroubleshooting(id)
        {
            axios.post(`/api/troubleshootings/${id}/update`, {
                id_application: this.app_id,
                PartName: this.PartName,
                Work: this.Work,
                Сonclusion: this.Сonclusion
            })
                .then(res => {
                    this.getTroubleshutings(res)
           })

        },
        createTroubleshooting()
        {
             axios.post(`/api/troubleshootings/create`, {
                id_application: this.app_id, PartName: this.PartName_Cr, Work: this.Work_Cr,  Сonclusion: this.Сonclusion_Cr
            })
           .then(res => {
            this.getTroubleshutings(res)
           })
        },

        editPartName(id)
        {
           let part_name = document.getElementById('Part_Name_'+id);
            this.PartName = part_name.innerText;
            console.log(part_name)
            this.updateTroubleshooting(id)
        },
        editСonclusion(id)
        {
           let сon = document.getElementById('Сonclusion_'+id);
            this.Сonclusion = сon.innerText;
            console.log(con)
            this.updateTroubleshooting(id)
        },
        editWork(id)
        {
            let wor = document.getElementById('Work_'+id);
            this.Work = wor.innerText;
            console.log(wor)
            this.updateTroubleshooting(id)
        }




    }

	}

</script>
