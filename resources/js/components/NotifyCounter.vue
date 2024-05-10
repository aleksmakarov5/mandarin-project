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
        'id_app',
        'count_in'
    ],
    data()
    {
      return{
        count: this.count_in,
        user_id:this.user,
        id: this.id_app
      }
    },

  created()
  {
   	Echo.channel('chat').listen('MessageSent', (e)=>{  
      if(e.notification.id_application==this.id)
     { if(e.notification.id_user==this.user_id)
      {if(e.notification.id_text==2)
        {
          this.count.chat=this.count.chat+1
        }
        else
        {
          if(e.notification.id_text==1)
          {
            this.count.chat=this.count.message+1
          } 
          else
          {
            this.count.application=this.count.application+1
          }
        }

      }}
            console.log(e) 
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