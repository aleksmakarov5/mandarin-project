import "./bootstrap";
import { createApp } from "vue";

import NotifyCounter from "./components/NotifyCounter.vue";
import NotifyButtons from "./components/NotifyButtons.vue";
import Zayavki from "./components/Zayavki.vue";
import PostNotify from "./components/PostNotify.vue";
import Chat from "./components/Chat.vue";
import NotifyApp from "./components/NotifyApp.vue";
import NextStep from "./components/NextStep.vue";
import Troubleshootings from "./components/Troubleshootings.vue";
import Estimate from "./components/Estimate.vue";
import FactWork from "./components/FactWork.vue";
import PlanWork from "./components/PlanWork.vue";
import Work from "./components/Work.vue";
import WorkPaint from "./components/WorkPaint.vue";
import SparePartMrk from "./components/SparePartMrk.vue";
import SparePartZap from "./components/SparePartZap.vue";
import SparePartAll from "./components/SparePartAll.vue";
import SparePartService from "./components/SparePartService.vue";
import SparePartKm from "./components/SparePartKm.vue";
import SendMail from "./components/SendMail.vue";
import NewMail from "./components/NewMail.vue";
import SpecialMail from "./components/SpecialMail.vue";
import AnswerMail from "./components/AnswerMail.vue";
import DatePay from "./components/DatePay.vue";
import FileUpload from "./components/FileUpload.vue";
import Warehouses from "./components/Warehouses.vue";
import AiAnaliz from "./components/AiAnaliz.vue";
import InventoryItems from "./components/InventoryItems.vue";
import Counterparties from "./components/Counterparties.vue";
import DropDown from "./components/DropDown.vue";
import StatusDroDow from "./components/StatusDroDow.vue";
import Gallery from "./components/Gallery.vue";
import Realiz from "./components/Realiz.vue";
import CheckDateView from "./components/CheckDateView.vue";
import ZpVedBuh from "./components/ZpVedBuh.vue";

const app1 = createApp({
    components: {
        NotifyCounter,
        PostNotify,
    },
});
app1.mount("#app1");

const app = createApp({
    components: {
        NotifyCounter,
        Zayavki,
        Troubleshootings,
        Estimate,
        FactWork,
        PlanWork,
        Work,
        WorkPaint,
        SparePartMrk,
        SparePartZap,
        SparePartAll,
        SparePartService,
        SparePartKm,
        NotifyApp,
        NextStep,
        DatePay,
        SendMail,
        NewMail,
        SpecialMail,
        AnswerMail,
        FileUpload,
        Warehouses,
        InventoryItems,
        Counterparties,
        AiAnaliz,
        Gallery,
        Realiz,
        CheckDateView,
        StatusDroDow,
        ZpVedBuh,
    },
});
app.mount("#app");

const nb = createApp({
    components: {
        NotifyButtons,
        DropDown,
    },
});
nb.mount("#nb");

const ch = createApp({
    components: {
        Chat,
    },
});
ch.mount("#ch");
