<style>
.gal_img {
    margin: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;
    background-color: white;
}
.gal_img:hover {
    box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    margin: 0;
}
.backclose {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 2;
}
.back_close_tr {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 5;
}
.gal_show {
    position: fixed;
    top: 1%;
    left: 1%;
    width: 98%;
    height: 98%;
    z-index: 3;
    align: center;
}
.gal_show img {
    display: block;
    margin: auto;
    height: 99%;
    max-width: 99%;
    max-height: 99%;
    border-radius: 5px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.move_left {
    position: fixed;
    top: 50%;
    left: 0;
    z-index: 8;

    opacity: 50%;
}
.move_right {
    position: fixed;
    top: 50%;
    right: 0;
    z-index: 8;

    opacity: 50%;
}
.move_left:hover {
    opacity: 100%;
}
.move_right:hover {
    opacity: 100%;
}
.move_left img:hover {
    width: 50px;
}
.move_right img:hover {
    width: 50px;
}
.gal_show_name {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 8;

    color: white;
    font-size: 24px;
    font-weight: bold;
}
.gal_show_button {
    padding: 5px;
    position: absolute;
    display: flex;
    top: 90;
    left: 0;
    z-index: 13;

    font-weight: bold;
}
.gal_show_button img {
    background-color: white;
    margin: 5px;
}
.gal_show_button img:hover {
    width: 50px;
    cursor: pointer;
}
.close {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 8;

    font-weight: bold;
}
.close img:hover {
    width: 30px;
}
.show_tr {
    position: fixed;
    top: 10%;
    left: 10%;
    width: 80%;
    height: 80%;
    z-index: 6;
    padding-top: 25px;
    background-color: white;
    border-radius: 5px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    overflow-y: scroll;
}
.delete_but {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 2;
    background-color: white;
    border-radius: 5px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.delete_but img:hover {
    width: 50px;
    cursor: pointer;
}
</style>
<template>
    <center>
        <h1>{{ name }}</h1>
    </center>

    <div class="row">
        <div class="col-sm-6 col-md-2" v-for="(image, key) in images">
            <div :class="'gal_img'">
                <img
                    :src="'/' + dir + '/' + image"
                    :alt="image"
                    width="100%"
                    @click="makeCurrent(key)"
                />
                <div class="delete_but">
                    <img
                        @click="deletePhoto(image)"
                        src="/storage/img/delete.png"
                        width="30px"
                        title="Удалить картину"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="backclose" v-if="current >= 0" @click="current = -1"></div>
    <div class="gal_show" v-if="current >= 0">
        <div class="gal_show_name">
            {{ images[current] }}
            <div class="gal_show_button">
                <img
                    @click="deletePhoto(images[current])"
                    src="/storage/img/delete.png"
                    width="30px"
                    title="Удалить картину"
                />
                <img
                    src="/storage/img/move.png"
                    width="30px"
                    title="Скопировать в другой архив"
                />
                <img
                    v-if="doc_type == 30"
                    @click="show_troubleshootings = true"
                    src="/storage/img/add.png"
                    width="30px"
                    title="Добавить запись в акт скрытых повреждений"
                />
            </div>
        </div>
        <div class="close" @click="current = -1">
            <img src="/storage/img/close.png" width="20px" />
        </div>
        <div class="move_left" @click="moveLeft()">
            <img src="/storage/img/move_left.png" width="40px" />
        </div>
        <div class="move_right" @click="moveRight()">
            <img src="/storage/img/move_right.png" width="40px" />
        </div>
        <img :src="'/' + dir + '/' + images[current]" :alt="images[current]" />
    </div>

    <div
        class="back_close_tr"
        v-if="show_troubleshootings"
        @click="show_troubleshootings = false"
    ></div>
    <div v-if="show_troubleshootings" class="show_tr">
        <troubleshootings
            :app_id="app_id"
            :in_troubleshootings="Troubleshootings"
            :in_img="images[current]"
            @getTroubleshutings="thisGetTroubleshutings"
        />
    </div>
</template>

<script>
import Troubleshootings from "./Troubleshootings.vue";
export default {
    components: {
        Troubleshootings,
    },

    props: [
        "app_id",
        "in_images",
        "dir",
        "name",
        "location",
        "doc_type",
        "doc_id",
        "troubleshootings",
    ],

    data() {
        return {
            current: -1,
            show_troubleshootings: false,
            Troubleshootings: this.troubleshootings,
            images: this.in_images,
        };
    },

    mounted() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.current = -1;
            }
            if (e.key === "ArrowLeft") {
                this.moveLeft();
            }
            if (e.key === "ArrowRight") {
                this.moveRight();
            }
        });
    },
    methods: {
        deletePhoto(image) {
            if (confirm("Вы уверены, что хотите удалить картину?")) {
                axios
                    .post("/api/application/photo_destroy", {
                        app_id: this.app_id,
                        image: image,
                        dir: this.dir,
                        location: this.location,
                        doc_id: this.doc_id,
                    })
                    .then((response) => {
                        // console.log(response);
                        this.images = response.data;
                        this.current = -1;
                        if ("caches" in window) {
                            caches.keys().then(function (cacheNames) {
                                // console.log(cacheNames);
                                cacheNames.forEach(function (cacheName) {
                                    caches.delete(cacheName);
                                });
                            });
                        }

                        location.reload(true);
                    });
            }
        },
        thisGetTroubleshutings(data) {
            this.Troubleshootings = data;
        },
        makeCurrent(key) {
            this.current = key;
            console.log(this.current);
        },
        moveLeft() {
            if (this.current > 0) {
                this.current--;
            } else {
                this.current = this.images.length - 1;
            }
        },
        moveRight() {
            if (this.current < this.images.length - 1) {
                this.current++;
            } else {
                this.current = 0;
            }
        },
    },
};
</script>
