import './bootstrap';
import { createApp } from 'vue';


import IncrementCounter from './components/IncrementCounter.vue';
import NotifyCounter from './components/NotifyCounter.vue';
import PostNotify from './components/PostNotify.vue';



const app1 = createApp({
  components: {
    IncrementCounter,
    NotifyCounter,
    PostNotify
    
  },
})
app1.mount('#app1')

const app = createApp({
  components: {
    IncrementCounter,
    NotifyCounter,
    
    
  },
})
app.mount('#app')
 
