<style>

.content{
  align-items: center;
  justify-content: center;
  height: 100%;
  width:80%;
  background: #f2f2f2;
  padding: 40px;
  z-index: 7;
  overflow-y: auto;
  border-radius: 5px;
   box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(113, 94, 94, 0.19);

}
.new_message {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    width: 100%;
    height: 350px;
    padding: 5px;
    overflow-y: scroll;
}
.send-button {
    margin-top: 40px;
    cursor: pointer;
}
</style>
<template>


<div class="content">
    <div>
<label for="to">Получатель</label> <img class="remove" src="/storage/img/add.png" title="Добавить получателя" width="30" alt="" @click.prevent="addTo()">
            <br>
        <template v-for="(t,i) in to">
              <input type="text" v-model="to[i]" size="100">
              <img v-if="i>0" class="remove" src="/storage/img/delete.png" title="Удалить получателя" width="20" alt="" @click.prevent="removeTo(i)">
              <br>
         </template>
    </div>

<label for="to">Тема письма</label><br>
     <input type="text" v-model="subject" size="100"></input>
     <br>
         <label for="text">Текст сообщения</label>
<div class="new_message" contenteditable>

</div>
<div class="send-button" @click.prevent="sendMessage()" title="Отправить сообщение">
       <img src="/storage/img/send_mess.png" width="30px">
        </div>
</div>



</template>

<script>
export default{

    props: [
        'in_to',
        'in_from',
        'in_subject',
        'in_attach',
        'in_app_id',
        'in_status',
    ],

    data()
    {
        return {
            subject: this.in_subject,
            to: this.in_to,
            from: this.in_from,
            attach: this.in_attach,
            app_id: this.in_app_id,
            status: this.in_status,

      }
    },


    mounted() {

        },
    methods: {
        addTo() {
        this.to.push(' ');
        },
    removeTo(index) {
        this.to.splice(index, 1);
        },


  }

}
</script>
