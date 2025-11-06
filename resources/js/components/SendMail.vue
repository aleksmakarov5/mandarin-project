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
.book{
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    position: fixed;
    background-color: white;
    width: 80%;
    height: 80%;
    padding: 5px;
    z-index: 7;
    overflow-y: scroll;
}
.close {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
    width: 20px;
    z-index: 8;
}
</style>
<template>
<div class="book" v-if="showBook">
    <div class="close" @click="showBook=false">
                <img src="/storage/img/close.png" width="20px" />
            </div>
               <h3>Добавить нового</h3>
            <table class="table table-striped">
<thead>
        <tr>
        <th>ФИО</th>
        <th>Email</th>
        <th>Компания</th>
        <th>Город</th>
        <th></th>
        </tr>
    </thead>
    <tbody>
        <tr >
           <td> <input type="text" v-model="name" placeholder="ФИО"></input></td>
            <td> <input type="text" v-model="email" placeholder="Email"></input></td>
            <td> <input type="text" v-model="company" placeholder="Компания"></input></td>
            <td> <input type="text" v-model="city" placeholder="Город"></input></td>
<td><input class="nav-link" type="button" value="Добавить" @click="addUser()"></input></td>
        </tr>
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
        <th>ФИО</th>
        <th>Email</th>
        <th>Компания</th>
        <th>Город</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="user in book" @click="selectBook(user.email)">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.company }}</td>
            <td>{{ user.city }}</td>
        </tr>
    </tbody>
</table>
</div>


<div class="content">

<label for="to">Получатель</label> <img class="remove" src="/storage/img/add.png" title="Добавить получателя" width="30" alt="" @click.prevent="addTo()">
            <br>
        <template v-for="(t,i) in to">
              <input type="text" v-model="to[i]" size="100" @click="editTo(i)"></input>
              <img class="remove" src="/storage/img/delete.png" title="Удалить получателя" width="20" alt="" @click.prevent="removeTo(i)">
              <br>
         </template>


<label for="to">Тема письма</label><br>
     <input type="text" v-model="subject" size="100"></input>
     <br>
<label for="text">Текст сообщения</label>
<div id="message_new" contenteditable class="new_message"  @keyup="editMessage()">
<span v-html="text"></span>
</div>
<div class="row">
                                        <template v-for="(doc,key) in attach">

                                                <div v-if="doc.ext!='gif'" class="card" style="width: 20rem;" @click="editAttach(doc.location,doc.id)" :style="docSelected(doc.location)">
                                                    <div class="row g-0">
                                                        <div class="col-md-4">


                                                                 <img :src="'/storage/img/'+doc.ext+'.png'"
                                                                    class="card-img-top"
                                                                   >

                                                        </div>

                                                        <div class="col-md-8">

                                                            <div class="card-body">

                                                                <h5 class="card-title">{{ doc.file_name }}</h5>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>

                                        </template>
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
        'text'
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
            message: '',
            location: [],
            doc_id: [],
            showBook: false,
            book: [],
            toIndex: 0,
            name: '',
            city: '',
            company: '',
            email: '',
        }
    },
    computed: {
        docSelected()
        {
            return function (location) {
                if (this.location.includes(location)) {
                    return 'border: 2px solid #000000;'
                }
            }
        }
    },
    methods: {
        editTo(index)
        {
            this.toIndex = index;
      }
    },


    mounted() {
        this.getBook();
        },
    methods: {
        addTo() {
        this.to.push(' ');
        },
        editMessage()
        {
            let mess = document.getElementById('message_new');
            this.message = mess.innerHTML;

        },
        sendMessage() {
            let mess = document.getElementById('message_new');
             this.message = mess.innerHTML;

            // mess.innerHTML = ''
            let to = []
            for (let i = 0; i < this.to.length; i++) {
                to.push(this.to[i])
            }
            if (this.message.trim == '')
                return
            else {
                let formData = new FormData();
                for (let i = 0; i < to.length; i++) {
                    formData.append('to[' + i + ']', to[i])
                }
                 for (let i = 0; i < this.location.length; i++) {
                    formData.append('location[' + i + ']', this.location[i])
                    formData.append('doc_id[' + i + ']', this.doc_id[i])
                 }


                formData.append('message', this.message)
                formData.append('id_application', this.app_id)
                formData.append('user_id', this.from)
                formData.append('subject', this.subject)
                formData.append('app_status', this.status)
                axios.post(`/api/mail/send`, formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                )

                    .then(res => {
                        //console.log(this.to)
                        console.log(res.data);
                        mess.innerHTML = '';
                        location.reload()
                    })
            }

        },

        editAttach(location,id)
        {
            if(this.location.includes(location))
            {
                this.location.splice(this.location.indexOf(location), 1);
            }
            else
            {
                this.location.push(location);
            }
            if(this.doc_id.includes(id))
            {
                this.doc_id.splice(this.doc_id.indexOf(id), 1);
            }
            else
            {
                this.doc_id.push(id);
            }

        },
        docSelected(location)
        {
            if(this.location.includes(location))
            {
                return "background: lightblue;"
            }
            else
            {
                return ""
            }

        },


    removeTo(index) {
        this.to.splice(index, 1);
    },

    editTo(i)
    {
            this.toIndex = i;
            this.showBook = true;
        },
        getBook()
        {
            axios.get(`/api/mail/book`)
                .then(res => {
                    console.log(res.data)
                    this.book = res.data
                })
        },
        selectBook(email)
        {
            this.to[this.toIndex] = email;
            this.showBook = false;
        },
        addUser()
        {
            let formData = new FormData();
            formData.append('name', this.name);
            formData.append('city', this.city);
            formData.append('company', this.company);
            formData.append('email', this.email);
            axios.post(`/api/mail/addUser`, formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }
            )
                .then(res => {
                    console.log(res.data);
                    this.getBook();
                    this.name = '';
                    this.city = '';
                    this.company = '';
                    this.email = '';
                    this.book.unshift(res.data)
                })
        },




  }

}
</script>
