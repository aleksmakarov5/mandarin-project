<template>


    <div class="chat" v-if="show">
        <div class="close" @click="show=!show">

            <img  src="/storage/img/close.png" width="20px">

        </div>
        <div class="chat-content">


        <div v-for="chat in chats" :key="chat.id" :class="styleId(chat.from)">
            <div class="from" v-if="this.user_id !=chat.from">{{ chat.from_name }}</div>
            <div v-if="chat.to_name" class="to">
                {{ chat.to_name }}
            </div>

            <div class="text">
                <span v-html="chat.message"></span>
            </div>
            <div class="time">{{ chat.time }}</div>
        </div>

        </div>
        <div class="contact-list" v-if="contact_show">

            <div class="contact-body">
    <div :id="'find_'+index" class="contact-item" v-for="(user,index) in users" @click="selectContact(user.id,user.name)">
        <template v-if="user.id == user_id" >
    Себе
    </template>
    <template v-else>{{ user.name }}</template>
    </div>
    </div>
</div>
        <div class="message-new" @keyup.@="contactShow()">

        <div id="message_" class="send-message" contenteditable @keydown="findContact()" @keyup.enter="editMessage()" @blur="editMessage()"
         @keydown.delete="deleteContact()" @focus="contact_show=false">

        </div>
        <div class="message-button" @click.prevent="sendMessage()">
       <img src="/storage/img/send_mess.png" width="30px">
        </div>
        </div>


    </div>
    <div class="chat-button" @click="showChat()" :class="styleButton()">
<p class="badge badge-green" v-if="count.chat!=0">{{count.chat}} </p>
            <img src="/storage/img/chat.png" width="30px">

    </div>


</template>

<script>

export default {
    props: [
        "in_chats",
        'user_id',
        'in_users',
        'app_id',
        'count_in',


    ],

    data() {

        return {
            chats: this.in_chats,
            show: false,
            message: '',
            to: [],
            contact_show: false,
            users: this.in_users,
            count: this.count_in,
            dotLength: 0,
            dotLengthHTML:0,
            is_find: false,

        }
    },
    created()
  {
    	Echo.channel('chat').listen('MessageSent', (e)=>{
            this.getCounts()
    }
    )

  },
    methods: {getCounts(){
            axios.get(`/api/notifycount/application/${this.app_id}/${this.user_id}`)
            .then(res => {
                this.count = res.data

             })

    },
        contactShow() {
            this.contact_show = true
            let mess = document.getElementById('message_');
            let text = mess.innerText;
            let message = mess.innerHTML

            this.dotLength = text.length
            this.dotLengthHTML = message.length
                this.is_find=true

      },
        styleButton() {
            if (this.show)
                return "d-none"

        },
        styleId(from) {
            if (this.user_id == from)
                return "right"
            else
                return "left"
        },
        editMessage() {
            let mess = document.getElementById('message_');
            this.message = mess.innerText;
        },
        selectContact(id, name) {
            this.to.push({ id: id, name: name });

            let mess = document.getElementById('message_');
            mess.innerHTML = mess.innerHTML.substring(0,this.dotLengthHTML);
            mess.innerHTML += '<b><span style="color:green;">' + name + '</span></b>&nbsp';
            mess.focus();

            const range = document.createRange();
            range.selectNodeContents(mess);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            this.contact_show = false;
            this.is_find=false
        },
        deleteContact() {
            let mess = document.getElementById('message_');
                 let message = mess.innerHTML
            let text = mess.innerText
                 if(text.length<(this.dotLength+1))
                 {
                     this.contact_show = false
                        this.is_find=false
                     }

            let position = this.getCursorPosition()
             for (let i = 0; i < this.to.length; i++) {
                let string = '<b><span style="color:green;">' + this.to[i].name + '</span></b>&nbsp';

                 let indexText = text.indexOf(this.to[i].name)
                let index = message.indexOf(string)
                 let res = position - this.to[i].name.length

                if ((indexText == (position - this.to[i].name.length - 1))||indexText == (position - this.to[i].name.length )) {
                    mess.innerHTML = message.slice(0, index-1)+message.slice(index+1+string.length, message.length)
                    this.to.splice(i, 1);
                    this.setCursorPosition(mess,position)

                }
            }
        },
        getCursorPosition() {
	        let selection = window.getSelection()
            let range = new Range
            let mess = document.getElementById('message_');
	        range.setStart(mess, 0)
	        range.setEnd(selection.anchorNode, selection.anchorOffset)
	        return range.toString().length
        },
        setCursorPosition(parent, position) {
            let child = parent.firstChild
            while (position > 0) {
	        let length = child.textContent.length
		        if(position > length) {
			        position -= length
			        child = child.nextSibling
		        }
		        else {
			        if(child.nodeType == 3) return document.getSelection().collapse(child, position)
			        child = child.firstChild
		        }
	         }
         },
        sendMessage() {
            let mess = document.getElementById('message_');
            this.message = mess.innerHTML
            let to=[]
            if (this.message.trim == '')
                return
            else {
                for (let i = 0; i < this.to.length; i++) {
                    to[i] = this.to[i].id
                }
                axios.post(`/api/chat/send`, {
                id_application: this.app_id,
                message: this.message,
                to: to,
                from: this.user_id,
                 })
           .then(res => {
               this.chats = res.data;
                    mess.innerHTML = '';
                    this.to = []
           })

            }
        },
        showChat() {
            this.show = !this.show;
            axios.post(`/api/chat/readed`, {
                id_application: this.app_id,
                user_id: this.user_id,

                   })
                .then(res => {
            this.count.chat=0

           })


        },
        findContact() {
            if (this.is_find)
            {
            let mess = document.getElementById('message_');
            let message = mess.innerText;
                let value = mess.innerText.substring(this.dotLength, message.length);
            let regPhrase = new RegExp(value, 'i');
            let flag = false;
                for (let i = 0; i < this.users.length; i++)
                {
                    flag = false;
            let div= document.getElementById('find_'+i)
                flag = regPhrase.test(div.innerText);
                if (flag) {
                    div.style.display = "";
                } else {
                    div.style.display = "none";
                }

                }

            }
        }


    }
}



</script>
