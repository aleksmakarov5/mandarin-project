import './bootstrap';
import { createApp } from 'vue';


import NotifyCounter from './components/NotifyCounter.vue';
import NotifyButtons from './components/NotifyButtons.vue';
import NotifyApp from './components/NotifyApp.vue';
import PostNotify from './components/PostNotify.vue';
import NextStep from './components/NextStep.vue';
import Troubleshootings from './components/Troubleshootings.vue';
import Estimate from './components/Estimate.vue';



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
    Estimate  
  },
})
app.mount('#app')

const nb = createApp({
  components: {
    NotifyButtons 
  },
})
nb.mount('#nb')

const na = createApp({
  components: {
    NotifyApp,
    NextStep
  },
})
na.mount('#na')
const ns = createApp({
  components: {
    NextStep
  },
})
ns.mount('#ns')
 
