<template>


    <div v-if="show" class="next-step">
		{{ this.txt }} 
    </div>
    <button v-on:click="show=!show" @blur="show=!show" :style="styleButton()">{{ show ? '-' : '?' }}</button>


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
        methods:{
            styleButton() {
                if (this.show)
                return "margin-left: 120px"
                
            }
        }
	  
       
    }
       
	
    
</script>