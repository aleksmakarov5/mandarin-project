<template>
 <div>
  
    {{id}}
    <p class="badge badge-blue" v-if="unreadable-30>10">{{unreadable-30}}</p>
    <p class="badge badge-red" v-if="unreadable!=0">{{unreadable+2}}</p>
 </div>
</template>

<script>
export default{
    
    props: [
        'unreadable_count',
        'id_app'
    ],
    data()
    {
      return{
        unreadable: this.unreadable_count,
        id: this.id_app
      }
    },

  created()
  {
   
    Echo.private(`notif.${this.id}`).listen('Notify', (e)=>{
      this.unreadable = e.notify
    }
    )
    
  }

}
</script>