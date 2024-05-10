import './bootstrap';
import { createApp } from 'vue';


import NotifyCounter from './components/NotifyCounter.vue';
import NotifyButtons from './components/NotifyButtons.vue';
import PostNotify from './components/PostNotify.vue';
import Troubleshootings from './components/Troubleshootings.vue';



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
    Troubleshootings,
    
    
  },
})
app.mount('#app')

app1.mount('#app1')

const nb = createApp({
  components: {
    NotifyButtons 
  },
})
nb.mount('#nb')
 
