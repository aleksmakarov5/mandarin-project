<template>
 <div class="badge-buttons">
  <template v-if="type==1">
    <p class="badge badge-blue" v-if="count.application!=0">{{count.application}}</p>
  </template>
  <template v-if="type==2">
    <p class="badge badge-red" v-if="count.message!=0">{{count.message}}</p>
    </template>
  <template v-if="type==3">
    <p class="badge badge-green" v-if="count.chat!=0">{{count.chat}}</p>
    </template>
 </div>
</template>

<script>
export default{
    
    props: [
      'user',
        'id_app',
        'type_in'
    ],
    data()
    {
      return{
        count: [],
        user_id:this.user,
        id: this.id_app,
type:this.type_in,
      }
    },

  created()
  {
    	Echo.channel('chat').listen('MessageSent', (e)=>{
            this.getCounts()   
    }
    )
    
  },
  mounted(){
            this.getCounts()
            
        },
  methods:{
        getCounts(){
            axios.get(`/api/notifycount/application/${this.id}/${this.user_id}`)
            .then(res => {
                this.count=res.data
             })

        },}

}
</script>