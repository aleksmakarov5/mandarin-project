<style>

.special-main
{
    top: 0;
    left: 0;
position:fixed;
  align-items: center;
  justify-content: center;
height: 100%;
width: 100%;
padding-left: 10%;
padding-top: 5%;
padding-bottom: 5%;
z-index: 2;
background-color: #6b696987;
}
.special-mail-button {

    padding: 10px;
    background-color: #6b6969;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
}

.special-mail-button:hover {
    background-color: #eb6700;
    color: white;
}
</style>
<template>
     <div class="special-mail-button" @click="showForm()">

            <img src="/storage/img/new-mail.png" width="20px">

    </div>

 <div class="special-main"  v-if="show_form">
    <div class="back" @click="showForm()"></div>
        <send-mail :in_to="to"
        :in_from="from"
        :in_subject="subject"
        :in_attach="attach"
        :in_app_id="app_id"
        :in_status="status"
        :text="'Партнеры, добрый день!<br><br>'+text+'<br><br><br><br>'+sign"
        />
 </div>
</template>

<script>
import SendMail from '../components/SendMail.vue';
export default {
        components: {
            SendMail,
        },

    props: [
        'to',
        'from',
        'subject',
        'attach',
        'app_id',
        'status',
        'sign',
        'text'
    ],

    data()
    {
        return {
            show_form:false,
      }
    },


    mounted() {

        },
    methods: {
    showForm() {
            this.show_form = !this.show_form;
        console.log(this.show_form)
    }


  }

}
</script>
