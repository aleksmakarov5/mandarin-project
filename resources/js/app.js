import './bootstrap';
import { createApp } from 'vue';


import NotifyCounter from './components/NotifyCounter.vue';
import NotifyButtons from './components/NotifyButtons.vue';
import NotifyApp from './components/NotifyApp.vue';
import PostNotify from './components/PostNotify.vue';
import NextStep from './components/NextStep.vue';
import Troubleshootings from './components/Troubleshootings.vue';
import Estimate from './components/Estimate.vue';
import FactWork from './components/FactWork.vue';
import Work from './components/Work.vue';

import SparePartMrk from './components/SparePartMrk.vue';
import SparePartZap from './components/SparePartZap.vue';
import SparePartAll from './components/SparePartAll.vue';
import Chat from './components/Chat.vue';
import DatePay from './components/DatePay.vue';







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
    Estimate,
    FactWork,
    Work,
    SparePartMrk,
    SparePartZap,
        SparePartAll,

     NotifyApp,
        NextStep,
    DatePay
  },
})
app.mount('#app')

const nb = createApp({
  components: {
    NotifyButtons
  },
})
nb.mount('#nb')

const ch = createApp({
  components: {
    Chat,
  },
})
ch.mount('#ch')



