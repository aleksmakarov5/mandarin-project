<template>


    <div v-if="show" class="next-step">
		{{ mytext }}
    </div>
    <button v-on:click="show=!show">{{ show ? 'Скрыть подсказку' : 'Показать подсказку' }}</button>


</template>

<script>

	export default{
        props:[
            'txt'
        ],

        data(){
            
            return{
                mytext:this.txt,
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