<template>

<div v-if="notifications[0]">
    <div v-if="show" class="mytoast-body">
		<div class="mytoast-item"  v-for="(notify,index) in notifications" :key="notify">
            <b>
                <button @click="deleteNotify(notify.id)">Х</button>
                {{ notify.text }}{{ notify.id_application }}</b>
                <a @click="deleteNotify(notify.id)" :href=notify.link+notify.id_application class="nav-link">Посмотреть</a>


	    </div>
    </div>
    <button v-on:click="show=!show">{{ show ? 'Скрыть уведомления' : 'Показать уведомления' }}</button>
</div>

</template>

<script>

	export default{
        props:[
            'user',
            'notification_in'
        ],

        data(){

            return{
                notifications:this.notification_in,
                show:false
            }
        },
	    created(){
        // this.getNotifications()
        console.log(this.notifications)

	    	Echo.channel('chat').listen('MessageSent', (e)=>{
                this.show=true;
            this.notifications.unshift(e.notification)
            console.log(e)
    }
    )

	    },
        mounted(){
            // this.getNotifications()

        },
    methods:{
        getNotifications(){
            axios.get(`/api/notifications/${this.user}`)
            .then(res => {
                this.notifications=res.data
            })

        },
        deleteNotify(id){
            axios.delete(`/api/notifications/${id}`)
                .then(res => {
                    this.notifications=res.data

                })
        }
    }

	}

</script>
