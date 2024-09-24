<template>


    <div v-if="show" class="next-step">
		{{ this.txt }} 
    </div>
    <button v-on:click="show=!show">{{ show ? 'Скрыть подсказку' : '?' }}</button>


</template>

<script>

	export default{
    props: [
            "txt",
    ],

        data(){
            
            return{
                mytext:this.txt,
                show:false
            }
        },
	  
       
    }
       
	
    
</script>