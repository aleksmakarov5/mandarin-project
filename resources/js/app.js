import './bootstrap';
import { createApp } from 'vue';


import NotifyCounter from './components/NotifyCounter.vue';
import PostNotify from './components/PostNotify.vue';



const app1 = createApp({
  components: {
    NotifyCounter,
    PostNotify
    
  },
})
app1.mount('#app1')

const app = createApp({
  components: {
    NotifyCounter,
    
    
  },
})
app.mount('#app')
 
