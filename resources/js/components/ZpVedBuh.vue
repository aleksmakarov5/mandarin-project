<style>
.click {
    cursor: pointer;
}
</style>
<template>
    <label for="mounth">Месяц</label>
    <select v-model="mounth" class="form-cont" placeholder="Месяц">
        <option value="01">Январь</option>
        <option value="02">Февраль</option>
        <option value="03">Март</option>
        <option value="04" selected>Апрель</option>
        <option value="05">Май</option>
        <option value="06">Июнь</option>
        <option value="07">Июль</option>
        <option value="08">Август</option>
        <option value="09">Сентябрь</option>
        <option value="10">Октябрь</option>
        <option value="11">Ноябрь</option>
        <option value="12">Декабрь</option>
    </select>

    <label for="year">Год</label>
    <select v-model="year" id="year" class="form-cont" placeholder="Год">
        <option value="2025">2025</option>
        <option value="2026" selected>2026</option>
    </select>
    <button class="nav-link" @click="showZP()">Показать</button>
    <h1>
        Зарплатная ведомость МРК
        <span v-if="start"
            >c {{ formatDate(start) }} по {{ formatDate(finish) }}</span
        >
    </h1>
    <table class="editable">
        <tr>
            <th colspan="7">ФИО</th>
            <th>% по умолч</th>
            <th>Итого</th>
        </tr>
        <tr>
            <th>№</th>

            <th>Авто</th>
            <th>ГРЗ</th>
            <th>Сумма от СК</th>
            <th>З/Ч</th>
            <th>Работы</th>
            <th>Остаток</th>
            <th>%</th>
            <th>ЗП</th>
        </tr>
        <template v-for="(aps, key) in apps">
            <template v-if="!gate || user_id == key">
                <tr class="click" @click="show[key] = !show[key]">
                    <td colspan="7">
                        <h3>{{ aps[0].mrk_name }}</h3>
                    </td>
                    <td>
                        <h5>{{ aps[0].def_mrk_proc }}%</h5>
                    </td>
                    <td>
                        <h2>{{ makeMoney(summ[key]) }}</h2>
                    </td>
                </tr>
                <tr v-for="application in aps" v-if="show[key]">
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.id }}
                    </td>
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.CarModel }}
                    </td>
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.gnz }}
                    </td>
                    <td>
                        {{ application.summ_ic_full }}
                    </td>
                    <td>
                        {{ application.summ_order }}
                    </td>
                    <td>
                        {{ application.summ_service }}
                    </td>

                    <td>
                        {{ makeMoney(application.eco_delta_full) }}
                    </td>
                    <td
                        class="click"
                        v-if="!edit_proc[application.id]"
                        @click="
                            !gate
                                ? (edit_proc[application.id] =
                                      !edit_proc[application.id])
                                : ''
                        "
                    >
                        {{ parseInt(getMrkProc(application) * 100) }}%
                    </td>
                    <td v-else>
                        <input
                            type="text"
                            v-model="proc[application.id]"
                            @blur="editProc(application.id)"
                            @keydown.enter="editProc(application.id)"
                        />
                    </td>
                    <td>
                        {{
                            makeMoney(
                                countZPmin(
                                    application.eco_delta_full,
                                    application
                                )
                            )
                        }}
                    </td>
                </tr>
            </template>
        </template>
    </table>
    <h1>
        Зарплатная ведомость Наставники
        <span v-if="start"
            >c {{ formatDate(start) }} по {{ formatDate(finish) }}</span
        >
    </h1>
    <table class="editable">
        <tr>
            <th colspan="7">ФИО</th>
            <th>% по умолч</th>
            <th>Итого</th>
        </tr>
        <tr>
            <th>№</th>

            <th>Авто</th>
            <th>ГРЗ</th>
            <th>Сумма от СК</th>
            <th>З/Ч</th>
            <th>Работы</th>
            <th>Остаток</th>
            <th>%</th>
            <th>ЗП</th>
        </tr>
        <template v-for="(aps, key) in apps_spm">
            <template v-if="!gate || user_id == key">
                <tr
                    v-if="key != 0"
                    class="click"
                    @click="show_spm[key] = !show_spm[key]"
                >
                    <td colspan="7">
                        <h3>{{ aps[0].spm_name }}</h3>
                    </td>
                    <td><h5>10%</h5></td>
                    <td>
                        <h2>{{ makeMoney(summ_spm[key]) }}</h2>
                    </td>
                </tr>
                <tr v-for="application in aps" v-if="show_spm[key]">
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.id }}
                    </td>
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.CarModel }}
                    </td>
                    <td class="click" @click="openApplication(application.id)">
                        {{ application.gnz }}
                    </td>
                    <td>
                        {{ application.summ_ic_full }}
                    </td>
                    <td>
                        {{ application.summ_order }}
                    </td>
                    <td>
                        {{ application.summ_service }}
                    </td>

                    <td>
                        {{ makeMoney(application.eco_delta_full) }}
                    </td>
                    <td>10%</td>
                    <td>
                        {{ makeMoney(application.eco_delta_full * 0.1) }}
                    </td>
                </tr>
            </template>
        </template>
    </table>
</template>
<script>
export default {
    props: ["user_id", "gate"],
    data() {
        return {
            applications: "",
            mounth: "",
            year: "",
            apps: "",
            apps_spm: "",
            summ: [],
            summ_spm: [],
            show_spm: [],
            show: [],
            $start: "",
            $finish: "",
            edit_proc: [],
            proc: [],
        };
    },
    methods: {
        getRes(res) {
            this.start = res.data[0];
            this.finish = res.data[1];
            this.apps = res.data[2];
            this.apps_spm = res.data[3];
            // console.log(this.apps_spm);
            for (let key in this.apps) {
                let summ = 0;
                // console.log(this.apps[key]);
                for (let i in this.apps[key]) {
                    summ =
                        parseFloat(summ) +
                        parseFloat(this.apps[key][i].eco_delta_full) *
                            parseFloat(this.getMrkProc(this.apps[key][i]));

                    this.edit_proc[this.apps[key][i].id] = false;
                    this.proc[this.apps[key][i].id] = parseInt(
                        this.getMrkProc(this.apps[key][i]) * 100
                    );
                }
                this.summ[key] = summ;
                this.show[key] = false;
                // console.log(summ);
            }
            for (let key in this.apps_spm) {
                let summ = 0;
                // console.log(this.apps[key]);
                for (let i in this.apps_spm[key]) {
                    if (key) {
                        summ =
                            parseFloat(summ) +
                            parseFloat(this.apps_spm[key][i].eco_delta_full) *
                                parseFloat(
                                    this.getMrkProc(this.apps_spm[key][i])
                                );
                    }
                    this.summ_spm[key] = summ;
                    this.show_spm[key] = false;
                }
                // console.log(summ);
            }
        },
        editProc(id) {
            axios
                .post(`/api/application/edit_proc`, {
                    id: id,
                    mrk_proc: this.proc[id],
                    mounth: this.mounth,
                    year: this.year,
                    user_id: this.user_id,
                    gate: this.gate,
                })
                .then((res) => {
                    // console.log(res.data);
                    this.getRes(res);
                });
        },
        showZP() {
            axios
                .post(`/api/application/show_zp`, {
                    mounth: this.mounth,
                    year: this.year,
                    user_id: this.user_id,
                    gate: this.gate,
                })
                .then((res) => {
                    // console.log(res.data);
                    this.getRes(res);
                });
        },
        countZPmin(eco_delta_full, app) {
            return parseFloat(eco_delta_full) * this.getMrkProc(app);
        },
        openApplication(id) {
            window.open(`/application/${id}`, "_blank");
        },
        openApp(id) {
            window.open(`/application/${id}`, "_blank");
        },
        makeCount(n) {
            return parseFloat(n)
                .toFixed(2)
                .replace(/(\d)(?=(\d{3})+\.\,)/g, "$1 ");
        },
        getMrkProc(app) {
            // console.log(app);
            let proc = 0;
            if (!app.mrk_proc) {
                proc = parseFloat(app.def_mrk_proc);
                if (!app.id_spm) proc += 4;
            } else {
                proc = parseFloat(app.mrk_proc);
            }
            proc /= 100;
            return proc;
        },

        makeMoney(n) {
            if (!n) n = 0;
            return (
                parseFloat(n)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ") + " р."
            );
        },
        formatDate(date) {
            let options = {
                year: "numeric",
                month: "numeric",
                day: "numeric",
            };
            if (date) return new Date(date).toLocaleString("ru", options);
            else return "";
        },
    },
};
</script>
