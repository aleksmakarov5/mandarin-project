<style>
input.in_file {
    position: absolute;
    top: -500px;
}
div.file_listing {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    padding: 5px;
    overflow-y: scroll;
    position: fixed;
    top: 15%;
    max-height: 70%;
}
.remove_file {
    color: red;
    cursor: pointer;
}
.add_attachment {
    height: 30px;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
}
.add_attachment:hover {
    background-color: #555;
    color: white;
    opacity: 0.2;
    transition: opacity 0.2s ease-in-out;
}
.green {
    background-color: aquamarine;
}
</style>
<template>
    <div>
        <input
            type="file"
            class="in_file"
            id="files_add"
            ref="files_add"
            multiple
            accept="image/png, image/gif, image/jpeg"
            v-on:change="handleFilesUpload()"
        />
    </div>
    <div class="file_listing" v-if="files_add.length > 0">
        <div>
            <span
                ><a @click.prevent="fileUpload()" class="nav-link"
                    >Загрузить</a
                ></span
            >
            <span
                ><a @click.prevent="cancel()" class="nav-link">отмена</a></span
            >
        </div>
        <div class="list" v-for="(file, key) in files_add">
            <!-- <img :src="file" width="50" /> -->
            <div :class="colors[key]">{{ file.name }}</div>
            <div>
                <img
                    class="remove_file"
                    title="Удалить вложение"
                    src="/storage/img/delete.png"
                    width="20"
                    @click="removeFile(key)"
                />
            </div>
        </div>
    </div>

    <div class="add_attachment" @click.prevent="addFiles()">
        <a title="Добавить картинку">
            <img src="/storage/img/attachment.png" width="30px"
        /></a>
    </div>
    <div v-if="showLoad">Загружено {{ count }} изображений</div>
</template>
<script>
export default {
    props: ["load_name", "load_location", "id_app", "status_id"],

    data() {
        return {
            files_add: [],
            showLoad: false,
            colors: [],
            count: 0,
        };
    },
    methods: {
        handleFilesUpload() {
            let uploadedFiles = this.$refs.files_add.files;
            for (var i = 0; i < uploadedFiles.length; i++) {
                this.files_add.push(uploadedFiles[i]);
                this.colors.push(" ");
            }
        },
        removeFile(key) {
            this.files_add.splice(key, 1);
            this.colors.splice(key, 1);
        },
        addFiles() {
            this.$refs.files_add.click();
        },
        cancel() {
            this.files_add = [];
        },
        fileUpload() {
            this.showLoad = true;
            for (var i = 0; i < this.files_add.length; i++) {
                let formData = new FormData();
                let file = this.files_add[i];
                formData.append("file", file);
                formData.append("load_name", this.load_name);
                formData.append("load_location", this.load_location);
                formData.append("id_app", this.id_app);
                formData.append("status_id", this.status_id);
                // console.log(formData);
                axios
                    .post("/api/application/file_upload", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((res) => {
                        // console.log(res);
                        this.colors[i] = "red";
                        count = i;
                        // console.log(this.colors[i]);
                    });
            }
            this.files_add = [];
            this.showLoad = false;
        },
    },
};
</script>
