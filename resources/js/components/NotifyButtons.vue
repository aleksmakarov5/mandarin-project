<template>
 <div class="badge-buttons">
  
   
    <p class="badge badge-blue" v-if="count.application!=0">{{count.application}}</p>
    <p class="badge badge-red" v-if="count.message!=0">{{count.message}}</p>
    <p class="badge badge-green" v-if="count.chat!=0">{{count.chat}}</p>
 </div>
</template>

<script>
export default{
    
    props: [
      
      'user',
      'pi',
    ],
    data()
    {
      return{
        count: [],
        user_id:this.user,
        p:this.pi,

      }
    },

  created()
  {
   this.getCounts()
   	Echo.channel('chat').listen('MessageSent', (e)=>{
            this.getCounts()   
    }
    )
    
  },
  mounted(){
    console.log(this.pi)
            this.getCounts()
            
        },
  methods:{
        getCounts(){
            axios.get(`/api/notifycount/${this.p}/${this.user_id}`)
            .then(res => {
                this.count=res.data
                console.log(res)
            })

        },}

}
</script>