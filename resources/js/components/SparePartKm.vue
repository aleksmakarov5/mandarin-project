<template>
    <div>
        <button class="accordion-est" @click="show = !show">Запчасти</button>
        <div :class="show ? '' : 'd_none'">
            <a :href="'/SparePart/' + app_id + '/doc_load'" class="nav-link"
                >Загрузить данные из калькуляции</a
            >
            <table class="editable" @blur="changeEditSparePartId(null)">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>
                        <th scope="col">Кат. номер</th>
                        <th scope="col">Зак. номер</th>

                        <th scope="col">Цена СК</th>
                        <th v-if="type">Скидка</th>
                        <th v-if="type">Цена <br />со скидкой</th>
                        <th scope="col">Цена ПЗН</th>

                        <th scope="col">Оригинал</th>
                        <th scope="col">Аналог</th>
                        <th scope="col">Б/у</th>
                        <th scope="col">СТО</th>
                        <th scope="col">Emex</th>
                        <th scope="col">AP</th>
                        <th scope="col">EA</th>

                        <th scope="col">Разница</th>

                        <th scope="col">Комментарий</th>
                        <template v-if="rout_it == 'directions'">
                            <th scope="col">Статус</th>
                            <th scope="col">Дата заказа</th>
                            <th scope="col">Ожидаемая дата</th>
                        </template>

                        <th scope="col"></th>
                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">{{ makeMoney(summ_ic_full) }}</th>

                        <th
                            v-if="type"
                            contenteditable
                            id="skidka"
                            @blur="editSkid(id)"
                            @keydown.enter="editSkid(id)"
                        >
                            {{
                                "undefined" !== typeof spareparts[0]
                                    ? spareparts[0].iznos
                                    : 0
                            }}
                        </th>
                        <th v-if="type">{{ makeMoney(summ_ic_half) }}</th>
                        <th scope="col">{{ makeMoney(summ_sto) }}</th>
                        <th scope="col" colspan="7"></th>
                        <th scope="col">
                            <div
                                :class="
                                    classDanger(
                                        type ? delta_res_half : delta_res_full
                                    )
                                "
                            >
                                {{
                                    makeMoney(
                                        type ? delta_res_half : delta_res_full
                                    )
                                }}
                            </div>
                        </th>

                        <th scope="col" colspan="2"></th>
                        <template v-if="rout_it == 'directions'">
                            <th scope="col"></th>

                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(sparepart, index) in spareparts">
                        <tr
                            :class="isAuthor(sparepart.author)"
                            tabindex="index"
                            @click="
                                changeEditSparePartId(
                                    sparepart.id,
                                    sparepart.name,
                                    sparepart.part_number,
                                    sparepart.order_number,
                                    sparepart.price_ic_full,
                                    sparepart.price_sto,
                                    sparepart.price_original,
                                    sparepart.price_analogue,
                                    sparepart.price_used,
                                    sparepart.comment,
                                    sparepart.status_id,
                                    sparepart.date_price,
                                    sparepart.date_wait
                                )
                            "
                        >
                            <td scope="col">
                                {{ index + 1 }}
                            </td>
                            <td
                                :id="'name_' + sparepart.id"
                                contenteditable
                                @blur="editName(sparepart.id, index)"
                                @keydown.enter="editName(sparepart.id, index)"
                            >
                                {{ sparepart.name }}
                            </td>
                            <td
                                :id="'part_number_' + sparepart.id"
                                contenteditable
                                @blur="editPartNumber(sparepart.id, index)"
                                @keydown.enter="
                                    editPartNumber(sparepart.id, index)
                                "
                            >
                                {{ sparepart.part_number }}
                            </td>
                            <td
                                :id="'order_number_' + sparepart.id"
                                contenteditable
                                @blur="editOrderNumber(sparepart.id, index)"
                                @keydown.enter="
                                    editOrderNumber(sparepart.id, index)
                                "
                            >
                                {{ sparepart.order_number }}
                            </td>
                            <td
                                class="ic_price"
                                :id="'price_ic_full_' + sparepart.id"
                                contenteditable
                                @blur="editPriceIcFull(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceIcFull(sparepart.id, index)
                                "
                            >
                                {{ makeCount(sparepart.price_ic_full) }}
                            </td>
                            <td
                                v-if="type"
                                class="ic_price"
                                :id="'iznos_' + sparepart.id"
                                contenteditable
                                @blur="editIznos(sparepart.id, index)"
                                @keydown.enter="editIznos(sparepart.id, index)"
                            >
                                {{ makeProc(sparepart.iznos) }}
                            </td>
                            <td
                                v-if="type"
                                class="ic_price"
                                :id="'price_ic_half_' + sparepart.id"
                                contenteditable
                                @blur="editPriceIcHalf(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceIcHalf(sparepart.id, index)
                                "
                            >
                                {{ makeCount(sparepart.price_ic_half) }}
                            </td>
                            <td
                                class="ic_price"
                                :id="'price_sto_' + sparepart.id"
                                contenteditable
                                @blur="editPriceSto(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceSto(sparepart.id, index)
                                "
                            >
                                {{
                                    makeCount(
                                        sparepart.price_sto &&
                                            sparepart.price_sto != 0
                                            ? sparepart.price_sto
                                            : type
                                            ? sparepart.price_ic_half
                                            : sparepart.price_ic_full
                                    )
                                }}
                            </td>
                            <td
                                :class="
                                    classSelect(
                                        1,
                                        sparepart.selected_mrk,
                                        sparepart.selected_price
                                    )
                                "
                                @contextmenu.prevent="
                                    updateSparePartZap(
                                        sparepart.id,
                                        1,
                                        sparepart.name,
                                        index
                                    )
                                "
                                :id="'price_original_' + sparepart.id"
                                contenteditable
                                @blur="editPriceOriginal(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceOriginal(sparepart.id, index)
                                "
                            >
                                {{ makeCount(sparepart.price_original) }}
                            </td>
                            <td
                                :class="
                                    classSelect(
                                        2,
                                        sparepart.selected_mrk,
                                        sparepart.selected_price
                                    )
                                "
                                @contextmenu.prevent="
                                    updateSparePartZap(
                                        sparepart.id,
                                        2,
                                        sparepart.name,
                                        index
                                    )
                                "
                                :id="'price_analogue_' + sparepart.id"
                                contenteditable
                                @blur="editPriceAnalogue(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceAnalogue(sparepart.id, index)
                                "
                            >
                                {{ makeCount(sparepart.price_analogue) }}
                            </td>
                            <td
                                :class="
                                    classSelect(
                                        3,
                                        sparepart.selected_mrk,
                                        sparepart.selected_price
                                    )
                                "
                                :id="'price_used_' + sparepart.id"
                                contenteditable
                                @contextmenu.prevent="
                                    updateSparePartZap(
                                        sparepart.id,
                                        3,
                                        sparepart.name,
                                        index
                                    )
                                "
                                @blur="editPriceUsed(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceUsed(sparepart.id, index)
                                "
                            >
                                {{ makeCount(sparepart.price_used) }}
                            </td>
                            <td
                                :class="
                                    classSelect(
                                        4,
                                        sparepart.selected_mrk,
                                        sparepart.selected_price
                                    )
                                "
                                :id="'price_service_' + sparepart.id"
                                contenteditable
                                @contextmenu.prevent="
                                    updateSparePartZap(
                                        sparepart.id,
                                        4,
                                        sparepart.name,
                                        index
                                    )
                                "
                                @blur="editPriceService(sparepart.id, index)"
                                @keydown.enter="
                                    editPriceService(sparepart.id, index)
                                "
                            >
                                <template
                                    v-if="sparepart.selected_service == 1"
                                >
                                    О
                                </template>
                                <template
                                    v-if="sparepart.selected_service == 2"
                                >
                                    А
                                </template>
                                <template
                                    v-if="sparepart.selected_service == 3"
                                >
                                    Б/У
                                </template>
                                {{ makeCount(sparepart.price_service) }}
                            </td>
                            <th>
                                <a
                                    :href="
                                        'https://emex.ru/products/' +
                                        sparepart.part_number
                                    "
                                    target="_blank"
                                    >-></a
                                >
                            </th>
                            <th>
                                <a
                                    :href="
                                        'https://autopiter.ru/goods/' +
                                        sparepart.part_number
                                    "
                                    target="_blank"
                                    >-></a
                                >
                            </th>
                            <th>
                                <a
                                    :href="
                                        'https://euroauto.ru/firms/all/' +
                                        sparepart.part_number +
                                        '/'
                                    "
                                    target="_blank"
                                    >-></a
                                >
                            </th>
                            <th>
                                <div :class="classDanger(sparepart.delta)">
                                    {{ makeCount(sparepart.delta) }}
                                </div>
                            </th>

                            <td
                                :id="'comment_' + sparepart.id"
                                contenteditable
                                @blur="editComment(sparepart.id, index)"
                                @keydown.enter="
                                    editComment(sparepart.id, index)
                                "
                            >
                                {{ sparepart.comment }}
                            </td>

                            <template v-if="rout_it == 'directions'">
                                <template v-if="isEdit(sparepart.id)">
                                    <td
                                        :class="
                                            sparePartStatus(sparepart.status_id)
                                        "
                                    >
                                        <select
                                            class="sel"
                                            id="status_id"
                                            name="status_id"
                                            v-model="
                                                spareparts[index].status_id
                                            "
                                            @change="
                                                updateSparePart(
                                                    sparepart.id,
                                                    index
                                                )
                                            "
                                        >
                                            <option value="0">------</option>
                                            <template
                                                v-for="status in statuses"
                                            >
                                                <option :value="status.id">
                                                    {{ status.StatusName }}
                                                </option>
                                            </template>
                                        </select>
                                    </td>
                                    <td width="450px">
                                        <input
                                            type="date"
                                            name="date_price"
                                            id="date_price"
                                            v-model="
                                                spareparts[index].date_price
                                            "
                                            @blur="
                                                updateSparePart(
                                                    sparepart.id,
                                                    index
                                                )
                                            "
                                            @keydown.enter="
                                                updateSparePart(
                                                    sparepart.id,
                                                    index
                                                )
                                            "
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="date"
                                            name="date_wait"
                                            id="date_wait"
                                            v-model="
                                                spareparts[index].date_wait
                                            "
                                            @blur="
                                                updateSparePart(
                                                    sparepart.id,
                                                    index
                                                )
                                            "
                                            @keydown.enter="
                                                updateSparePart(
                                                    sparepart.id,
                                                    index
                                                )
                                            "
                                        />
                                    </td>
                                </template>
                                <template v-else>
                                    <td
                                        :class="
                                            sparePartStatus(sparepart.status_id)
                                        "
                                    >
                                        {{ sparepart.StatusName }}
                                    </td>
                                    <td>
                                        {{ fortmatDate(sparepart.date_price) }}
                                    </td>
                                    <td>
                                        {{ fortmatDate(sparepart.date_wait) }}
                                    </td>
                                </template>
                            </template>
                            <td scope="col">
                                <a
                                    @click.prevent="
                                        deletesparepart(sparepart.id)
                                    "
                                >
                                    <img
                                        src="/storage/img/delete.png"
                                        width="20"
                                        alt="delete"
                                /></a>
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td scope="col">
                            <a @click.prevent="createSparePart()">
                                <img
                                    src="/storage/img/edit.png"
                                    width="30"
                                    alt="create"
                            /></a>
                        </td>
                        <td>
                            <input
                                type="text"
                                id="name"
                                v-model="name_Cr"
                                class="w-100"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "app_id",
        "ub_type",
        "rout",
        "in_statuses",
        "in_spareparts",
        "in_summ_sto",
        "in_summ_ic_full",
        "in_delta_res_full",
        "in_summ_ic_half",
        "in_delta_res_half",
    ],

    data() {
        return {
            spareparts: this.in_spareparts,
            id_app: this.app_id,
            rout_it: this.rout,
            EditSparePartId: null,
            type: this.ub_type,
            name: "",
            part_number: "",
            order_number: "",
            price_ic_full: "",
            price_original: "",
            price_analogue: "",
            price_used: "",
            price_sto: "",
            date_price: "",
            statusname: "",
            date_wait: "",
            status_id: "",
            comment: "",
            summ_sto: this.in_summ_sto,
            delta_res_full: this.in_delta_res_full,
            delta_res_half: this.in_delta_res_half,
            delta: 0,
            selected_price: "",
            selected_mrk: "",
            statuses: this.in_statuses,
            summ_ic_full: this.in_summ_ic_full,
            summ_ic_half: this.in_summ_ic_half,
            name_Cr: "",
            show: true,
        };
    },

    mounted() {
        // this.getSpareParts()
    },
    methods: {
        getSpareParts(res) {
            this.spareparts = res.data[0];
            this.summ_ic_full = res.data[1];
            this.summ_sto = res.data[2];
            this.delta_res_full = res.data[3];
            this.statuses = res.data[4];
            this.summ_ic_half = res.data[5];
            this.delta_res_half = res.data[6];
        },
        deletesparepart(id) {
            if (confirm("Точно удалить?")) {
                axios.delete(`/api/sparepart/${id}`).then((res) => {
                    this.getSpareParts(res);
                });
            }
        },
        //  @click="changeEditSparePartId(sparepart.id,sparepart.name,sparepart.part_number,sparepart.order_number,sparepart.price_ic_full,sparepart.price_sto,sparepart.price_original,sparepart.price_analogue,sparepart.price_used)"
        changeEditSparePartId(
            id,
            name,
            part_number,
            order_number,
            price_ic_full,
            price_sto,
            price_original,
            price_analogue,
            price_used,
            comment,
            status_id,
            date_price,
            date_wait
        ) {
            this.EditSparePartId = id;
            this.name = name;
            this.comment = comment;
            this.price_sto = price_sto;
            this.price_analogue = price_analogue;
            this.price_used = price_used;
            this.price_original = price_original;
            this.part_number = part_number;
            this.order_number = order_number;
            this.price_ic_full = price_ic_full;
            this.status_id = status_id;
            this.date_price = date_price;
            this.date_wait = date_wait;
        },
        isEdit(id) {
            return this.EditSparePartId === id;
        },
        updateSparePartZap(id, select, name, index) {
            axios
                .post(`/api/sparepart/${id}/update`, {
                    id_application: this.app_id,
                    name: name,
                    selected_price: select,

                    selected_mrk: select,
                })
                .then((res) => {
                    this.getSpareParts(res);
                });
        },
        updateSparePart(id, index) {
            axios
                .post(`/api/sparepart/${id}/update`, {
                    id_application: this.app_id,
                    name: this.spareparts[index].name,
                    comment: this.spareparts[index].comment,

                    price_sto: this.spareparts[index].price_sto,
                    price_analogue: this.spareparts[index].price_analogue,
                    price_used: this.spareparts[index].price_used,
                    price_original: this.spareparts[index].price_original,
                    price_service: this.spareparts[index].price_service,
                    part_number: this.spareparts[index].part_number,
                    order_number: this.spareparts[index].order_number,
                    price_ic_full: this.spareparts[index].price_ic_full,
                    price_ic_half: this.spareparts[index].price_ic_half,
                    iznos: this.spareparts[index].iznos,
                    status_id: this.spareparts[index].status_id,
                    date_price: this.spareparts[index].date_price,
                    date_wait: this.spareparts[index].date_wait,
                })
                .then((res) => {
                    this.getSpareParts(res);
                });
            this.changeEditSparePartId(null);
        },
        updateSparePartAll() {
            axios
                .post(`/api/sparepart/update_all`, {
                    id_application: this.app_id,
                    spareparts: this.spareparts,
                })
                .then((res) => {
                    this.getSpareParts(res);
                });
            this.changeEditSparePartId(null);
        },

        createSparePart() {
            axios
                .post(`/api/sparepart/create`, {
                    id_application: this.app_id,
                    name: this.name_Cr,
                    author: 5,
                })
                .then((res) => {
                    this.getSpareParts(res);
                });
            this.name_Cr = "";
        },
        makeMoney(n) {
            if (!n) n = 0;
            return (
                parseFloat(n)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ") + " р."
            );
        },
        makeCount(n) {
            if (!n) return "";
            if (n == "-") return "нет";
            let text = parseFloat(n)
                .toFixed(2)
                .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
            // text=text.substring(0, text.indexOf("."))
            if (text == "0") return "";
            else {
                return text;
            }
        },
        makeProc(n) {
            if (!n) return "";
            if (n == "-") return "нет";
            let text = parseFloat(n)
                .toFixed()
                .replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
            // text=text.substring(0, text.indexOf("."))
            if (text == "0") return "";
            else {
                return text;
            }
        },
        parseF(n) {
            if (!n) return 0;

            return parseFloat(n);
        },
        classDanger(n) {
            if (n < 0) return "alert alert-success";
            else return "alert alert-danger";
        },
        classSelect(n, mrk, zap) {
            if (mrk == n && zap == n) return "zapmrk";
            else if (zap == n) return "zap";
            else if (mrk == n) return "mrk";
            else return "";
        },
        isAuthor(author) {
            if (author == 5) return "pink";
        },
        sparePartStatus(status) {
            if (status == 1) return "yellow";
            else if (status == 2) return "green";
            else if (status == 3) return "teal";
            else if (status == 4) return "blue";
            else return "gray";
        },
        showTr(id, author) {
            if (this.isEdit(id)) return "d_none";
            else return this.isAuthor(author);
        },
        editName(id, index) {
            let name = document.getElementById("name_" + id);
            this.spareparts[index].name = name.innerText;
            this.updateSparePart(id, index);
        },
        editComment(id, index) {
            let comment = document.getElementById("comment_" + id);
            this.spareparts[index].comment = comment.innerText;
            this.updateSparePart(id, index);
        },
        editPriceUsed(id, index) {
            let price_used = document.getElementById("price_used_" + id);
            this.spareparts[index].price_used = price_used.innerText
                .split(" ")
                .join("");
            this.updateSparePart(id, index);
        },
        editPriceService(id, index) {
            let price_service = document.getElementById("price_service_" + id);
            this.spareparts[index].price_service = price_service.innerText
                .split(" ")
                .join("");
            this.updateSparePart(id, index);
        },
        editPriceAnalogue(id, index) {
            let price = document.getElementById("price_analogue_" + id);
            console.log(index, id, price.innerText.split(" ").join(""));
            this.spareparts[index].price_analogue = price.innerText
                .split(" ")
                .join("");
            this.updateSparePart(id, index);
        },
        editPriceOriginal(id, index) {
            let price_original = document.getElementById(
                "price_original_" + id
            );
            this.spareparts[index].price_original = price_original.innerText
                .split(" ")
                .join("");
            this.updateSparePart(id, index);
        },
        editPriceICFull(id, index) {
            let price_ic_full = document.getElementById("price_ic_full_" + id);
            this.spareparts[index].price_ic_full = price_ic_full.innerText
                .split(" ")
                .join("");
            this.updateSparePart(id, index);
        },
        editPriceSto(id, index) {
            let price_sto = document.getElementById("price_sto_" + id);
            this.spareparts[index].price_sto = price_sto.innerText
                .split(" ")
                .join("");

            let n = -1;
            let sum = 0;
            this.updateSparePart(id, index);

            for (let i = 0; i < this.spareparts.length; i++) {
                if (
                    this.spareparts[i].name.includes("МЕЛКИЕ ДЕТАЛИ") ||
                    this.spareparts[i].name.includes("мелкие детали") ||
                    this.spareparts[i].name.includes("Мелкие Детали")
                ) {
                    n = i;
                } else {
                    sum =
                        this.parseF(sum) +
                        this.parseF(
                            this.parseF(this.spareparts[i].price_sto) == 0
                                ? this.spareparts[i].price_ic_full
                                : this.spareparts[i].price_sto
                        );
                }
            }
            console.log(sum);
            if (n != -1) {
                this.spareparts[n].price_sto = this.parseF(sum) * 0.02;
                console.log(this.spareparts[n].id);
                this.updateSparePart(this.spareparts[n].id, n);
            }
        },

        editPartNumber(id, index) {
            let part_number = document.getElementById("part_number_" + id);
            this.spareparts[index].part_number = part_number.innerText;
            this.updateSparePart(id, index);
        },
        editOrderNumber(id, index) {
            let order_number = document.getElementById("order_number_" + id);
            this.spareparts[index].order_number = order_number.innerText;
            this.updateSparePart(id, index);
        },
        editPriceIcFull(id, index) {
            let price_ic_full = document.getElementById("price_ic_full_" + id);
            this.spareparts[index].price_ic_full = price_ic_full.innerText;
            this.updateSparePart(id, index);
        },
        editPriceIcHalf(id, index) {
            let price_ic_half = document.getElementById("price_ic_half_" + id);
            this.spareparts[index].price_ic_half = price_ic_half.innerText;
            this.updateSparePart(id, index);
        },
        editIznos(id, index) {
            let iznos = document.getElementById("iznos_" + id);
            this.spareparts[index].iznos = iznos.innerText;
            this.spareparts[index].price_ic_half =
                (this.parseF(this.spareparts[index].price_ic_full) / 100) *
                (100 - this.parseF(this.spareparts[index].iznos));
            this.updateSparePart(id, index);
        },

        editSkid(id) {
            let skidka = document.getElementById("skidka");
            for (let i = 0; i < this.spareparts.length; i++) {
                this.spareparts[i].iznos = skidka.innerText;
                this.spareparts[i].price_ic_half =
                    (this.parseF(this.spareparts[i].price_ic_full) / 100) *
                    (100 - this.parseF(this.spareparts[i].iznos));
            }
            this.updateSparePartAll();
        },

        fortmatDate(date) {
            let options = {
                year: "numeric",
                month: "long",
                day: "numeric",
            };
            if (date) return new Date(date).toLocaleString("ru", options);
            else return "";
        },
    },
};
</script>
