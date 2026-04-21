<style>
.gal_img {
    margin: 15px;
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
    max-width: 99%;
    max-height: 99%;
    border-radius: 5px;
    align: center;
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
.close img:hover {
    width: 30px;
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
            </div>
        </div>
    </div>
    <div class="backclose" v-if="current >= 0" @click="current = -1"></div>
    <div class="gal_show" v-if="current >= 0">
        <div class="gal_show_name">
            {{ images[current] }}
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
</template>

<script>
export default {
    props: ["app_id", "images", "dir", "name", "location"],

    data() {
        return {
            current: -1,
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
