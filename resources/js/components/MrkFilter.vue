<style></style>
<template>
    <div class="filter" @click="mrk_filter_show = !mrk_filter_show">
        <img src="/storage/img/filter.png" width="15px" />
    </div>
    <div
        class="back-filter"
        v-if="mrk_filter_show"
        @click="mrk_filter_show = !mrk_filter_show"
    ></div>
    <div class="city-filter" v-if="mrk_filter_show">
        <table class="table">
            <thead>
                <th>
                    <input
                        type="checkbox"
                        :checked="mrk_selected.length == mrk.length"
                        id="all_city"
                        @change="allMrkSelect()"
                    />
                </th>
                <th>Все</th>
            </thead>
            <tbody>
                <tr v-for="c in mrk">
                    <td>
                        <input
                            :checked="mrk_selected.includes(c.id)"
                            type="checkbox"
                            :id="'mrk_' + c.id"
                            @change="mrkSelectedChange(c.id)"
                        />
                    </td>
                    <td>{{ c.name }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ["user_id", "mrk_filter", "mrk"],

    data() {
        return {
            mrk_selected: [],
            mrk_filter_show: false,
        };
    },
    mounted() {
        for (let i = 0; i < this.mrk_filter.length; i++) {
            this.mrk_selected[i] = this.mrk_filter[i].id_mrk;
        }
        if (this.mrk_selected.length == 0) this.mrkSelected();
    },

    methods: {
        mrkSelectedChange(id) {
            if (this.mrk_selected.includes(id)) {
                axios
                    .post(`/api/application/delete_mrk_filter`, {
                        id_mrk: id,
                        id_user: this.user_id,
                    })
                    .then((res) => {
                        this.mrk_selected.splice(
                            this.mrk_selected.indexOf(id),
                            1
                        );
                    });
            } else {
                axios
                    .post(`/api/application/add_mrk_filter`, {
                        id_mrk: id,
                        id_user: this.user_id,
                    })
                    .then((res) => {
                        this.mrk_selected.push(id);
                    });
            }
        },

        allMrkSelect() {
            if (this.mrk_selected.length == this.mrk.length) {
                axios
                    .post(`/api/application/delete_mrk_filters`, {
                        id_user: this.user_id,
                    })
                    .then((res) => {
                        this.mrk_selected = [];
                    });
            } else {
                this.mrkSelected();
                axios.post(`/api/application/add_mrk_filters`, {
                    id_mrk: this.mrk_selected,
                    id_user: this.user_id,
                });
            }
        },
        mrkSelected() {
            this.mrk_selected = [];
            for (let i = 0; i < this.mrk.length; i++) {
                this.mrk_selected[i] = this.mrk[i].id;
            }
        },
    },
};
</script>
