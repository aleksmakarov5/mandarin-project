<template>

<div v-if="notifications[0]">
    <div v-if="show" class="mytoast-body">
		<div class="mytoast-item"  v-for="(notify,index) in notifications" :key="notify">
            <b>
                <button v-on:click="notifications.splice(index, 1)">Х</button>
                {{ notify.message }}{{ notify.app_id }}</b>
                <a :href=notify.link+notify.app_id class="nav-link">Посмотреть</a>
             
		
	    </div>
    </div>
    <button v-on:click="show=!show">{{ show ? 'Скрыть уведомления' : 'Показать уведомления' }}</button>
</div>

</template>

<script>

	export default{
        data(){
            return{
                notifications:[],
                show:true
            }
        },
	    created(){


	    	Echo.channel('chat').listen('MessageSent', (e)=>{
                this.show=true;
            this.notifications.unshift(e.notification)
            console.log(this.notifications)
    }
    )
        


	    }
       
	}
</script>