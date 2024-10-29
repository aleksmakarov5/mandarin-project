<template>
<div>
    <button class="accordion-est" @click="show=!show">
            Итого
        </button>
<div :class="show?'':'d_none'">

    <a :href="'/application/'+id_app+'/doc_load'" class="nav-link">Загрузить данные из калькуляции</a>

            <table class="editable">
                <tr :class="editSert?'d_none':''">

                        <th>Сертификат:</th>
                        <td @click="editEstimateSert(sert)">
                        {{ makeMoney(sert) }}
                        </td>

                </tr>
                <tr :class="editSert?'':'d_none'">
                    <th>Сертификат:</th>
                    <td>
                        <input type="text" id="sert" v-model="sert" class="w-100" @blur="updateEstimate(estimate.id)"
                        @keydown.enter="updateEstimate(estimate.id)" >
                    </td>
                </tr>

                <tr :class="editEmr?'d_none':''">

                        <th>ЕМР+10:</th>
                        <td  @click="editEstimateEmr(estimate.emr10)">
                        <template v-if=estimate.emr10>
                           Да
                        </template>
                        <template v-else=estimate.emr10>
                           нет
                        </template>
                        </td>

                </tr>
                <tr :class="editEmr?'':'d_none'">

                        <th>ЕМР+10:</th>
                        <td >
                        <select class=sel id="emr10" name="emr10"  v-model="emr10" @change="updateEstimate(estimate.id)"
                        >
                                <option value=0 >нет</option>
                                <option value=1 >да</option>
                            </select>
                        </td>

                </tr>

                    <tr :class="editSpFull ? 'd_none':''">
                        <th>Cтоимость узлов и деталей:</th>
                        <td @click="editEstimateSpFull(estimate.price_sp_full)">
                            {{ makeMoney(estimate.price_sp_full) }}
                        </td>
                    </tr>
                    <tr :class="editSpFull ? '':'d_none'">
                        <th>Cтоимость узлов и деталей:</th>
                        <td @click="editEstimateSpFull(estimate.price_sp_full)">
                            <input type="text" id="price_sp_full" v-model="price_sp_full" class="w-100" @blur="updateEstimate(estimate.id)"
                        @keydown.enter="updateEstimate(estimate.id)" >
                        </td>
                    </tr>

                    <tr :class="editSpHalf?'d_none':''">
                        <th>Cтоимость узлов и деталей c учетом износа:</th>
                        <td @click="editEstimateSpHalf(estimate.price_sp_half)">
                            {{ makeMoney(estimate.price_sp_half) }}
                        </td>
                    </tr>
                    <tr :class="editSpHalf ? '' :'d_none'">
                        <th>Cтоимость узлов и деталей c учетом износа:</th>
                        <td @click="editEstimateSpHalf(estimate.price_sp_half)">
                            <input type="text" id="price_sp_half" v-model="price_sp_half" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)" >

                        </td>
                    </tr>

                    <tr :class="editRepair?'d_none':''">
                        <th>Cтоимость ремонта/замены:</th>
                        <td  @click="editEstimateRepair(estimate.price_repair)">
                            {{ makeMoney(estimate.price_repair) }}

                        </td>
                    </tr>
                     <tr :class="editRepair?'':'d_none'">
                        <th>Cтоимость ремонта/замены:</th>
                        <td >
                            <input type="text" id="price_repair" v-model="price_repair" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)" >

                        </td>
                    </tr>

                    <tr  :class="editPaint?'d_none':''">
                        <th>Cтоимость окраски контроля: </th>
                        <td  @click="editEstimatePaint(estimate.price_paint)">
                            {{ makeMoney(estimate.price_paint) }}
                        </td>
                    </tr>
                    <tr :class="editPaint?'':'d_none'">
                        <th>Cтоимость окраски контроля: </th>
                        <td >
                            <input type="text" id="price_paint" v-model="price_paint" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)" >
                        </td>
                    </tr>

                    <tr :class="editZip?'d_none':''">
                        <th>Cтоимость расходных материалов: </th>
                        <td  @click="editEstimateZip(estimate.price_zip)">
                            {{ makeMoney(estimate.price_zip) }}
                        </td>
                    </tr>
                    <tr :class="editZip?'':'d_none'">
                        <th>Cтоимость расходных материалов: </th>
                        <td >
                            <input type="text" id="price_zip" v-model="price_zip" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)" >
                        </td>
                    </tr>


                    <tr :class="editWork?'d_none':''">
                        <th>Cтоимость работ и материалов: </th>
                        <td  @click="editEstimateWork(estimate.price_work)">
                            {{ makeMoney(estimate.price_work) }}
                        </td>
                    </tr>
                    <tr :class="editWork?'':'d_none'">
                        <th>Cтоимость работ и материалов: </th>
                        <td >
                            <input type="text" id="price_work" v-model="price_work" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)" >
                        </td>
                    </tr>

                    <tr :class="editRepairFull?'d_none':''">
                        <th>Cтоимость восстановительного ремонта (Всего) </th>
                        <td  @click="editEstimateRepairFull(estimate.price_repair_full)">
                            {{ makeMoney(estimate.price_repair_full) }}

                        </td>
                    </tr>
                    <tr :class="editRepairFull?'':'d_none'">
                        <th>Cтоимость восстановительного ремонта (Всего) </th>
                        <td>
                            <input type="text" id="price_repair_full" v-model="price_repair_full" class="w-100" @blur="updateEstimate(estimate.id)"
                     @keydown.enter="updateEstimate(estimate.id)">
                        </td>
                    </tr>
                    <tr>
                        <th>Фактическая стоимость восстановительного ремонта </th>
                        <td>{{ makeMoney(estimate.fact) }}</td>

                    </tr>
                    <tr>
                        <th>Разница от полной стоимости</th>
                        <th>
                          {{ makeMoney(estimate.price_repair_full-estimate.fact-sert) }}
                        </th>
                    </tr>


            </table>

</div>
</div>
</template>

<script>

	export default{
        props:[
            'app_id',
            'in_estimate',
            'in_sert'
        ],

        data(){

            return{
                estimate: this.in_estimate,
                id_app: this.app_id,
                editSpFull: false,
                editEmr: false,
                editSpHalf: false,
                editRepair: false,
                editPaint: false,
                editZip: false,
                editWork: false,
                editRepairFull: false,
                editSert: false,
                sert: this.in_sert,
                price_sp_full: this.in_estimate.price_sp_full,
                price_sp_half: this.in_estimate.price_sp_half,
                price_repair: this.in_estimate.price_repair,
                price_paint: this.in_estimate.price_paint,
                price_zip: this.in_estimate.price_zip,
                price_work: this.in_estimate.price_work,
                price_repair_full: this.in_estimate.price_repair_full,
                emr10:this.in_estimate.emr10,

                show: true,
            }
        },

        mounted(){
            if (!this.estimate.price_sp_full) {
                this.estimate.price_sp_full=0
            }
            if (!this.estimate.price_sp_half) {
                this.estimate.price_sp_half=0
            }
            if (!this.estimate.price_repair) {
                this.estimate.price_repair=0
            }
            if (!this.estimate.price_paint) {
                this.estimate.price_paint=0
            }
            if (!this.estimate.price_zip) {
                this.estimate.price_zip=0
            }
            if (!this.estimate.price_work) {
                this.estimate.price_work=0
            }
            if (!this.estimate.price_repair_full) {
                this.estimate.price_repair_full=0
            }
            if (!this.estimate.sert) {
                this.estimate.sert=0
            }


        },
    methods:{
        getEstimate(res){

                this.estimate = res.data
                this.emr10 = this.estimate.emr10
                this.price_sp_full = this.estimate.price_sp_full
                this.price_sp_half = this.estimate.price_sp_half
                this.price_repair = this.estimate.price_repair
                this.price_paint = this.estimate.price_paint
                this.price_zip = this.estimate.price_zip
                this.price_work = this.estimate.price_work
                this.price_repair_full = this.estimate.price_repair_full
                this.sert = this.estimate.sert
                console.log(this)


        },

        editEstimateSpFull(SpFull){
            this.price_sp_full = SpFull
        this.editSpFull=true
        },
        editEstimateEmr(emr)
        {
            this.emr10 = emr
            this.editEmr=true
        },
        editEstimateSpHalf(SpHalf) {
            this.price_sp_half = SpHalf
            this.editSpHalf=true
        },
        editEstimateRepair(Repair) {
            this.price_repair = Repair
            this.editRepair=true
        },
        editEstimatePaint(Paint) {
            this.price_paint = Paint
            this.editPaint=true
        },
        editEstimateZip(Zip) {
            this.price_zip = Zip
            this.editZip=true
        },
        editEstimateWork(Work) {
            this.price_work = Work
            this.editWork=true
        },
        editEstimateRepairFull(RepairFull) {
            this.price_repair_full = RepairFull
            this.editRepairFull=true
        },
        editEstimateSert(sert) {
            this.sert = sert
            this.editSert = true
        },


        isEdit(id){
            return this.EditEstimateId===id
        },
        updateEstimate(id)
        {
            axios.post(`/api/estimate/${id}/update`, {
                id_application: this.app_id,
                emr10: this.emr10,
                price_sp_full: this.price_sp_full,
                price_sp_half: this.price_sp_half,
                price_repair: this.price_repair,
                price_paint: this.price_paint,
                price_work: this.price_work,
                price_repair_full: this.price_repair_full,
                price_zip: this.price_zip,
                sert: this.sert
            })
           .then(res => {
               this.getEstimate(res)
               this.editSpFull = false
               this.editEmr = false
               this.editSpHalf = false
               this.editRepair = false
               this.editPaint = false
               this.editZip = false
               this.editWork = false
               this.editRepairFull = false
               this.editSert = false

           })


        },

        makeMoney(n) {
            if (!n) n = 0;
            return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")+' р.';
        }



    }

	}

</script>
