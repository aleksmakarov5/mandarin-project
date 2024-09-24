<template>


    <div v-if="show" class="next-step">
		{{ text }}
    </div>
    <button v-on:click="show=!show">{{ show ? 'Скрыть подсказку' : 'Показать подсказку' }}</button>


</template>

<script>

	export default{
        props:[
            'text'
        ],

        data(){
            
            return{
                text:this.text,
                show:false
            }
        },
	    created(){
         },
        mounted(){
            
        },
    methods:{
        

        },
       
    }
       
	
    
</script>