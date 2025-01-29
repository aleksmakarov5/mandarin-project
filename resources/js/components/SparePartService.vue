
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

                        <th scope="col">Цена СК</th>
                        <th scope="col">Цена сервиса</th>
                        <th scope="col">Вид ЗЧ</th>




                    <th scope="col">Комментарий</th>

                    </tr>



        </thead>
        <tbody>
<template v-for="(sparepart,index) in spareparts">
<tr :class="isAuthor(sparepart.author)"  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.selected_service,sparepart.price_service,sparepart.comment)">
    <td scope="col">
        {{ index+1 }}
    </td>
    <td  :id="'name_'+sparepart.id" contenteditable @blur="editName(sparepart.id)" @keydown.enter="editName(sparepart.id)">
        {{ sparepart.name }}</td>
    <td :id="'part_number_'+sparepart.id" contenteditable @blur="editPartNumber(sparepart.id)" @keydown.enter="editPartNumber(sparepart.id)">
        {{ sparepart.part_number }}</td>

        <td >
        {{ makeCount(sparepart.price_ic_full) }}</td>
         <td :class="classSelect(4,sparepart.selected_mrk,sparepart.selected_price)"  :id="'price_service_'+sparepart.id" contenteditable @blur="editPriceService(sparepart.id)" @keydown.enter="editPriceOriginal(sparepart.id)">
        {{ makeCount(sparepart.price_service) }}</td>
    <template v-if="isEdit(sparepart.id)">


        <td>
<select class=sel id="selected_service" name="selected_service" v-model="selected_service" @change="updateSparePart(sparepart.id)">
    <option value=0 >Оригинал</option>
    <option value=1 >Аналог</option>
    <option value=2 >Б/У</option>
</select>
        </td>
        </template>
        <template v-else>
            <td>
                <template v-if="sparepart.selected_service==0">
                    О
                </template>
                <template v-if="sparepart.selected_service==1">
                    А
                </template>
                <template v-if="sparepart.selected_service==2">
                    Б/У
                </template>
            </td>
        </template>


        <td  :id="'comment_'+sparepart.id" contenteditable @blur="editComment(sparepart.id)" @keydown.enter="editComment(sparepart.id)">
        {{ sparepart.comment }}</td>



</tr>
 </template>
<tr>
                    <td scope="col"><a  @click.prevent="createSparePart()">

                        <img src="/storage/img/edit.png" width="30" alt="create"></a>
                    </td>
                    <td><input type="text"  id="name" v-model="name_Cr" class="w-100"></td>
                    <td><input type="text"  id="part_number" v-model="part_number_Cr" class="w-100"></td>


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
            'in_spareparts',
        ],

        data(){

            return{
                spareparts:this.in_spareparts,
                id_app: this.app_id,
                EditSparePartId:null,
                name: '',
                selected_service: '',
                comment: '',
                name_Cr: '',
                part_number_Cr: '',
                show: true,

            }
        },

        mounted(){
            // this.getSpareParts()

        },
    methods:{
        getSpareParts(res){

                this.spareparts = res.data[0]



        },

        //  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.part_number,sparepart.order_number,sparepart.price_ic_full,sparepart.price_sto,sparepart.price_original,sparepart.price_analogue,sparepart.price_used)"
        changeEditSparePartId(id,name,selected_service,price_service,comment) {
            this.EditSparePartId=id
            this.comment = comment
            this.name = name
            this.price_service = price_service
            this.selected_service = selected_service


        },
        isEdit(id){
            return this.EditSparePartId===id
        },

        updateSparePart(id)
        {
            console.log(this.price_sto)
            axios.post(`/api/sparepart/${id}/update`, {
                id_application: this.app_id,
                name: this.name,
                comment: this.comment,
                price_service: this.price_service,
                selected_service: this.selected_service,

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
                 part_number: this.part_number_Cr
            })
           .then(res => {
            this.getSpareParts(res)
           })
            this.name_Cr = ''
            this.part_number_Cr = ''
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

        editPriceService(id)
        {
            let price_service = document.getElementById('price_service_'+id);
            this.price_service = price_service.innerText;
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
