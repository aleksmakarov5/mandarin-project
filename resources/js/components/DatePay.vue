
<template>
    <div>
    <table class="editable" >
        <thead>
            <tr>
                <th colspan="4">
                    <h4>Даты оплаты</h4>
                </th>
            </tr>

           <tr>
                        <th scope="col">СК</th>
                        <th scope="col">СТО</th>
                        <th scope="col">ЗП ведомость</th>
                        <th scope="col">Выплата ЗП</th>
                    </tr>



        </thead>
        <tbody>
            <tr>
                        <th scope="col" v-if="ic_pay_date_show" @click="ic_pay_date_show=false">&nbsp{{fortmatDate(ic_pay_date)}}</th>
                        <th scope="col" v-else>
                            <input type="date" name="ic_pay_date" id="ic_pay_date" v-model="ic_pay_date" @blur="updateDate()" @keydown.enter="updateDate()">
                        </th>

                        <th scope="col" v-if="sto_pay_date_show" @click="sto_pay_date_show=false">{{fortmatDate(sto_pay_date)}}</th>
                        <th scope="col" v-else>
                            <input type="date" name="sto_pay_date" id="sto_pay_date" v-model="sto_pay_date" @blur="updateDate()" @keydown.enter="updateDate()">
                        </th>
                        <th scope="col" v-if="zp_date_show" @click="zp_date_show=changeShow()">{{fortmatDate(zp_date)}}</th>
                        <th scope="col" v-else>
                            <input type="date" name="zp_date" id="zp_date" v-model="zp_date" @blur="updateDate()" @keydown.enter="updateDate()">
                        </th>

                        <th scope="col">{{fortmatDate(finish_pay_date)}}</th>
            </tr>


        </tbody>
    </table>
 </div>
</template>

<script>

	export default{
        props:[
            'app_id',
            'in_ic_pay_date',
            'in_sto_pay_date',
            'in_zp_date',
            'in_finish_pay_date'
        ],

        data(){

            return {
                ic_pay_date: this.in_ic_pay_date,
                sto_pay_date:this.in_sto_pay_date,
                zp_date:this.in_zp_date,
                finish_pay_date: this.in_finish_pay_date,
                ic_pay_date_show: true,
                sto_pay_date_show: true,
                zp_date_show: true

            }
        },

        mounted(){
            // this.getWorks()

        },
    methods:{
        getDate(res) {

            this.ic_pay_date = res.data[0]
            this.sto_pay_date = res.data[1]
            this.zp_date = res.data[2]
            this.finish_pay_date = res.data[3]


        },


        updateDate()
        {
            this.ic_pay_date_show = true,
             this.sto_pay_date_show = true,
             this.zp_date_show = true
            console.log(this.ic_pay_date)
            console.log(this.sto_pay_date)
            console.log(this.zp_date)





            axios.post(`/api/date/${this.app_id}/update`, {
                id: this.app_id,
                ic_pay_date: this.ic_pay_date,
                sto_pay_date: this.sto_pay_date,
                zp_date: this.zp_date
            })
                .then(res => {

            this.getDate(res)
           })

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
        },
        changeShow()
        {
            if (!this.finish_pay_date)
                return false
            else
                return true

        }

    }

	}

</script>
