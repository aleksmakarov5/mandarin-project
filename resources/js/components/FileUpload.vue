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
    top: 25%;
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
            accept="image/jpeg application/pdf"
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
            <span v-if="progress > 0">{{ progress }}%</span>
        </div>
        <div class="list" v-for="(file, key) in files_add">
            <!-- <img :src="file" width="50" /> -->
            <div :class="colors[key]">
                {{ itog[key] }} <span v-if="count > 0">{{ count }}</span
                >{{ file.name }}
            </div>
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
    <div v-if="showLoad">
        Загружено {{ count }} изображений
        <a :href="'/document/' + id_app" class="nav-link">Готово</a>
    </div>
</template>
<script>
export default {
    props: ["load_name", "load_location", "id_app", "status_id"],

    data() {
        return {
            files_add: [],
            showLoad: false,
            colors: [],
            itog: [],
            count: 0,
            progress: 0,
        };
    },
    methods: {
        handleFilesUpload() {
            let uploadedFiles = this.$refs.files_add.files;
            for (var i = 0; i < uploadedFiles.length; i++) {
                this.files_add.push(uploadedFiles[i]);
                this.colors.push(" ");
                this.itog.push(" ");
            }
        },
        removeFile(key) {
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
            let files_upload = this.files_add;
            for (var i = 0; i < files_upload.length; i++) {
                let formData = new FormData();
                let file = files_upload[i];
                formData.append("file", file);
                formData.append("load_name", this.load_name);
                formData.append("load_location", this.load_location);
                formData.append("id_app", this.id_app);
                formData.append("status_id", this.status_id);
                formData.append("num", i);

                // console.log(formData.getAll);
                const config = {
                    onUploadProgress: (progressEvent) => {
                        const progress = Math.round(
                            (progressEvent.loaded / progressEvent.total) * 100
                        );
                        this.progress = progress;
                        console.log(`File upload progress: ${progress}%`);
                    },
                    headers: { "Content-Type": "multipart/form-data" },
                    maxContentLength: 1000000000,
                    maxBodyLength: 1000000000,
                };
                this.sleep(500).then(() =>
                    axios
                        .post("/api/application/file_upload", formData, config)
                        .then((res) => {
                            console.log(res.data);
                            this.count++;
                            this.colors[res.data] = "green";
                            this.itog[res.data] = "Загружено";
                            this.files_add[res.data] = "";
                            // this.colors[res.data] = "red";
                            if (this.count == files_upload.length) {
                                this.files_add = [];
                                this.colors = [];
                            }

                            // console.log(this.colors[i]);
                        })
                        .catch((error) => {
                            // i--;
                        })
                );
            }

            // this.files_add = [];
            // this.showLoad = false;
        },
        sleep(ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
};
</script>
