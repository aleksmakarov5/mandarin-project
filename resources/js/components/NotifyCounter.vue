<template>
 <div>
  
    {{id}}
    <p class="badge badge-blue" v-if="count.application!=0">{{count.application}}</p>
    <p class="badge badge-red" v-if="count.message!=0">{{count.message}}</p>
    <p class="badge badge-green" v-if="count.chat!=0">{{count.chat}}</p>
 </div>
</template>

<script>
export default{
    
    props: [
      'user',
        'id_app'
    ],
    data()
    {
      return{
        count: [],
        user_id:this.user,
        id: this.id_app
      }
    },

  created()
  {
  //  this.getCounts()
   	Echo.channel('chat').listen('MessageSent', (e)=>{
            this.getCounts()   
    }
    )
    
  },
  mounted(){
            // this.getCounts()
            
        },
  methods:{
        getCounts(){
            axios.get(`/api/notifycount/application/${this.id}/${this.user_id}`)
            .then(res => {
                this.count=res.data
                console.log(res)
            })

        },}

}
</script>