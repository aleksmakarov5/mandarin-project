<style>
.chat-content {
    overflow-y: scroll;
    display: flex;
    flex-direction: column-reverse;
}
.contact-list {
    position: absolute;
    bottom: 180px;
    height: 170px;
    width: 90%;
    background-color: white;
    overflow-y: scroll;
    z-index: 6;
}
.add-work-group {
    position: absolute;
    bottom: 400px;
    max-height: 170px;
    width: 90%;
    background-color: white;
    overflow-y: scroll;
    z-index: 9;
}

input.in-file {
    position: absolute;
    top: -500px;
}

.remove-file {
    color: red;
    cursor: pointer;
}
.remove {
    cursor: pointer;
}
.remove:hover {
    opacity: 0.2;
    transition: opacity 0.2s ease-in-out;
}
.add-attachment {
    position: absolute;
    bottom: 105px;
    right: 40px;
    padding: 10px;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
}
.add-button {
    position: absolute;
    bottom: 105px;
    right: 15px;
    padding: 10px;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
}
.team-button {
    position: absolute;
    bottom: 65px;
    right: 15px;
    padding: 10px;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
}
.team-button:hover,
.add-button:hover,
.add-attachment:hover {
    background-color: #555;
    color: white;
    opacity: 0.2;
    transition: opacity 0.2s ease-in-out;
}

div.file-listing {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 10px;
    background-color: white;
    padding: 5px;
    overflow-y: scroll;
    position: absolute;
    top: 15px;
}
div.list {
    display: flex;
    justify-content: space-between;
}
div.attachment {
    display: block;
}
div.attachment img {
    margin-left: 5px;
    margin-bottom: 12px;
    cursor: pointer;
}
.attachment-show {
    background-color: rgba(255, 255, 255, 0.917);
    position: fixed;
    left: 15%;
    top: 5%;
    height: 90%;
    z-index: 7;
    overflow: auto;
}
.reply-show {
    background-color: rgba(255, 255, 255, 0.17);
    position: absolute;
    width: 95%;
    max-height: 10%;
    overflow: auto;
    z-index: 1;
}

.close {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
    width: 20px;
    z-index: 8;
}
div.attachment-show img {
    max-height: 100%;
    max-width: 100%;
}
.work-group-select {
    position: absolute;
    bottom: 120px;
    height: 270px;
    width: 98%;
    background-color: white;
    overflow-y: scroll;
    z-index: 3;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.7), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    padding: 5px;
}
.back {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.016);
    z-index: -1;
    display: flex;
    justify-content: center;
    align-items: center;
}
.group-back {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    z-index: -1;
    display: flex;
    justify-content: center;
    align-items: center;
}
.chat {
    position: fixed;
    left: 5px;
    bottom: 20px;
    z-index: 5;
    margin-right: 5px;
    background-color: #c7dbf9;
    padding: 10px;
    width: 450px;
    height: 90%;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 7px;
    max-width: 97%;
}
</style>
<template>
    <div class="chat" :style="show ? 'display: block;' : 'display: none;'">
        <div class="back" @click="show = false"></div>
        <div class="attachment-show" ref="attachment_show" v-if="show_attach">
            <div class="close" @click="show_attach = !show_attach">
                <img src="/storage/img/close.png" width="20px" />
            </div>
            <img id="attachment_img" :src="img_path" />
        </div>

        <div>
            <input
                type="file"
                class="in-file"
                id="files"
                ref="files"
                multiple
                accept="image/png, image/gif, image/jpeg"
                v-on:change="handleFilesUpload()"
            />
        </div>
        <div class="close" @click="show = !show">
            <img src="/storage/img/close.png" width="20px" />
        </div>
        <div
            class="chat-content"
            :style="replayShow ? 'max-height: 65%;' : 'max-height: 75%;'"
        >
            <div
                v-for="chat in chats"
                :key="chat.id"
                class="message-all"
                @mouseover="answerShow(chat.id)"
                @mouseout="answerHide(chat.id)"
            >
                <div :class="'chat-message ' + styleId(chat.from)">
                    <div class="from" v-if="this.user_id != chat.from">
                        {{ chat.from_name }}
                    </div>

                    <div class="text">
                        <span v-html="chat.message"></span>
                    </div>
                    <div class="time">{{ chat.time }}</div>
                    <div class="attachment" v-if="chat.attachment != null">
                        <template v-for="attachment in chat.attachment">
                            <img
                                :src="
                                    '/storage/files/' +
                                    app_id +
                                    '/' +
                                    attachment.location
                                "
                                width="60"
                                @click="attachmentShow(attachment.location)"
                            />
                        </template>
                    </div>
                </div>
                <div
                    :id="'answer_' + chat.id"
                    class="show-reply-button"
                    v-if="styleId(chat.from) != 'right'"
                >
                    <div
                        class="message-reply"
                        @click="
                            answerReply(
                                chat.from,
                                chat.from_name,
                                chat.message,
                                chat.time
                            )
                        "
                    >
                        <img src="/storage/img/reply.png" width="15px" />
                    </div>
                    <div class="message-reply" @click="unreaded(chat.id)">
                        <img src="/storage/img/unreaded.png" width="15px" />
                    </div>
                </div>
            </div>
            <div class="file-listing" v-if="files.length > 0">
                <div class="list" v-for="(file, key) in files">
                    <div>{{ file.name }}</div>
                    <div>
                        <img
                            class="remove-file"
                            title="Удалить вложение"
                            src="/storage/img/delete.png"
                            width="20"
                            @click="removeFile(key)"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-list" v-if="contact_show">
            <div class="contact-body">
                <div
                    :id="'find_' + index * 1000"
                    class="contact-item"
                    v-for="(group, index) in groups"
                    @click="selectGroup(group)"
                >
                    <i
                        ><b>{{ group.name }}</b></i
                    >
                </div>
                <div
                    :id="'find_' + index"
                    class="contact-item"
                    v-for="(user, index) in users"
                    @click="selectContact(user.id, user.name)"
                >
                    <template v-if="user.id == user_id"> Себе </template>
                    <template v-else>{{ user.name }}</template>
                </div>
            </div>
        </div>
        <div class="add-work-group" v-if="add_work_group_show">
            <div class="contact-body">
                <div
                    class="contact-item"
                    v-for="(group, index) in groups"
                    @click="selectGroupToWorkGroup(group)"
                >
                    <i
                        ><b>{{ group.name }}</b></i
                    >
                </div>
                <div
                    class="contact-item"
                    v-for="(user, index) in users"
                    @click="selectContactToWorkGroup(user.id)"
                >
                    {{ user.name }}
                </div>
            </div>
        </div>
        <div class="add-work-group" v-if="minus_work_group_show">
            <div class="contact-body">
                <div
                    class="contact-item"
                    v-for="(group, index) in groups"
                    @click="minusGroupToWorkGroup(group)"
                >
                    <i
                        ><b>{{ group.name }}</b></i
                    >
                </div>
            </div>
        </div>
        <div class="reply-show">
            <div class="close" @click="noReply()">
                <img src="/storage/img/close.png" width="20px" />
            </div>
            <div id="reply_"></div>
        </div>

        <div class="work-group-select" v-if="work_group_show">
            <div class="group-back" @click="work_group_show = false"></div>
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                :checked="
                                    work_group_selected.length ==
                                    work_groups.length
                                "
                                id="all_work_group"
                                @change="workGroupCheckAll()"
                            />
                        </th>
                        <th>Рабочая группа</th>
                        <th>
                            <img
                                class="remove"
                                src="/storage/img/add.png"
                                title="Добавить группу участника"
                                width="30"
                                alt=""
                                @click.prevent="
                                    add_work_group_show = !add_work_group_show
                                "
                            />
                            <img
                                class="remove"
                                src="/storage/img/minus.png"
                                title="Исключить группу участников"
                                width="30"
                                alt=""
                                @click.prevent="
                                    minus_work_group_show =
                                        !minus_work_group_show
                                "
                            />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in work_groups">
                        <td>
                            <input
                                title="Исключить из текущего сообщения"
                                :checked="work_group_selected.includes(user.id)"
                                class="remove"
                                type="checkbox"
                                :id="'work_group_' + user.id"
                                @change="workGroupChange(user.id)"
                            />
                        </td>
                        <td>
                            <label for="'work_group_'+group.id">{{
                                user.name
                            }}</label>
                        </td>
                        <td>
                            <img
                                class="remove"
                                src="/storage/img/delete.png"
                                width="25"
                                alt=""
                                title="Удалить участника"
                                @click="minusContactToWorkGroup(user.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="message-new" @keyup.@="contactShow()">
            <div
                id="message_"
                class="send-message"
                contenteditable
                @keydown="findContact()"
                @keyup.enter="editMessage()"
                @blur="editMessage()"
                @keydown.delete="deleteContact()"
                @focus="contact_show = false"
            ></div>
            <div class="add-button" @click.prevent="addDot()">
                <a title="Добавить получателя">
                    <img src="/storage/img/add.png" width="20px"
                /></a>
            </div>
            <div class="add-attachment" @click.prevent="addFiles()">
                <a title="Добавить картинку">
                    <img src="/storage/img/attachment.png" width="20px"
                /></a>
            </div>
            <div
                v-if="can_send"
                class="message-button"
                @click.prevent="sendMessage()"
            >
                <img src="/storage/img/send_mess.png" width="30px" />
            </div>
            <div v-if="!can_send" class="message-button">
                <img src="/storage/img/sending_chat.gif" width="30px" />
            </div>
            <!-- <div class="team-button" @click="work_group_show=!work_group_show">
             <a title="Рабочая группа">
            <img src="/storage/img/team.png" width="20px"></a>
        </div> -->
        </div>
    </div>

    <div class="chat-button" @click="showChat()" :class="styleButton()">
        <p class="badge badge-green" v-if="count.chat != 0">{{ count.chat }}</p>
        <img src="/storage/img/chat.png" width="30px" />
    </div>
</template>

<script>
export default {
    props: [
        "in_chats",
        "user_id",
        "in_users",
        "app_id",
        "count_in",
        "in_groups",
        "in_work_groups",
    ],

    data() {
        return {
            chats: this.in_chats.sort().reverse(),
            show: false,
            message: "",
            to: [],
            contact_show: false,
            users: this.in_users,
            groups: this.in_groups,
            count: this.count_in,
            dotLength: 0,
            dotLengthHTML: 0,
            is_find: false,
            reply: "",
            files: [],
            show_attach: false,
            img_path: "",
            add_work_group_show: false,
            work_group_show: false,
            work_groups: [], //this.in_work_groups,
            minus_work_group_show: false,
            work_group_selected: [],
            replayShow: false,
            can_send: true,
        };
    },
    created() {
        Echo.channel("chat").listen("MessageSent", (e) => {
            this.getCounts();
        });
    },
    mounted() {
        this.WorkGroupSelected();
    },
    methods: {
        workGroupCheckAll() {
            if (this.work_group_selected.length == this.work_groups.length) {
                this.work_group_selected = [];
            } else {
                this.WorkGroupSelected();
            }
        },
        workGroupChange(id) {
            if (this.work_group_selected.includes(id)) {
                this.work_group_selected.splice(
                    this.work_group_selected.indexOf(id),
                    1
                );
            } else {
                this.work_group_selected.push(id);
            }
        },
        WorkGroupSelected() {
            this.work_group_selected = [];
            for (let i = 0; i < this.work_groups.length; i++) {
                this.work_group_selected[i] = this.work_groups[i].id;
            }
        },
        addToWorkGroup() {},
        selectGroup(group) {
            let mess = document.getElementById("message_");

            for (let i = 0; i < group.members.length; i++) {
                this.to.push({
                    id: group.members[i].id,
                    name: group.members[i].name,
                });

                mess.innerHTML =
                    '@<b><span style="color:green;">' +
                    group.members[i].name +
                    "</span></b>&nbsp<br/>" +
                    mess.innerHTML;
            }
            mess.focus();

            const range = document.createRange();
            range.selectNodeContents(mess);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            this.contact_show = false;
            this.is_find = false;
        },
        getCounts() {
            axios
                .get(
                    `/api/notifycount/application/${this.app_id}/${this.user_id}`
                )
                .then((res) => {
                    this.count = res.data;
                });
        },
        addFiles() {
            this.$refs.files.click();
        },

        contactShow() {
            this.contact_show = true;
            let mess = document.getElementById("message_");
            let text = mess.innerText;
            let message = mess.innerHTML;

            this.dotLength = text.length;
            this.dotLengthHTML = message.length;
            this.is_find = true;
        },
        workGroupShow() {
            this.work_group_show = true;
        },

        styleButton() {
            if (this.show) return "d-none";
        },
        styleId(from) {
            if (this.user_id == from) return "right";
            else return "left";
        },
        editMessage() {
            let mess = document.getElementById("message_");
            this.message = mess.innerText;
        },
        addDot() {
            let mess = document.getElementById("message_");
            const range = document.createRange();
            range.selectNodeContents(mess);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            this.contactShow();
        },
        selectContact(id, name) {
            this.to.push({ id: id, name: name });

            let mess = document.getElementById("message_");
            mess.innerHTML = mess.innerHTML.substring(0, this.dotLengthHTML);
            mess.innerHTML =
                '@<b><span style="color:green;">' +
                name +
                "</span></b>&nbsp<br/>" +
                mess.innerHTML;
            mess.focus();

            const range = document.createRange();
            range.selectNodeContents(mess);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            this.contact_show = false;
            this.is_find = false;
        },
        selectContactToWorkGroup(id) {
            axios
                .post(`/api/chat/add_work_group`, {
                    id_application: this.app_id,
                    id_user: id,
                })
                .then((res) => {
                    this.work_groups = res.data;
                    this.add_work_group_show = false;
                    this.WorkGroupSelected();
                });
        },
        minusContactToWorkGroup(id) {
            if (confirm("Точно удалить?")) {
                axios
                    .post(`/api/chat/delete_work_group`, {
                        id_application: this.app_id,
                        id_user: id,
                    })
                    .then((res) => {
                        this.work_groups = res.data;
                        this.WorkGroupSelected();
                    });
            }
        },
        selectGroupToWorkGroup(group) {
            let gr = [];
            for (let i = 0; i < group.members.length; i++) {
                gr.push(group.members[i].id);
            }
            axios
                .post(`/api/chat/add_work_groups`, {
                    id_application: this.app_id,
                    group: gr,
                })
                .then((res) => {
                    this.work_groups = res.data;
                    this.add_work_group_show = false;
                    this.WorkGroupSelected();
                });
        },
        minusGroupToWorkGroup(group) {
            if (confirm("Точно исключить <" + group.name + ">?")) {
                let gr = [];
                for (let i = 0; i < group.members.length; i++) {
                    gr.push(group.members[i].id);
                }
                axios
                    .post(`/api/chat/delete_work_groups`, {
                        id_application: this.app_id,
                        group: gr,
                    })
                    .then((res) => {
                        this.work_groups = res.data;
                        this.minus_work_group_show = false;
                        this.WorkGroupSelected();
                    });
            }
        },
        deleteContact() {
            let mess = document.getElementById("message_");
            let message = mess.innerHTML;
            let text = mess.innerText;
            if (text.length < this.dotLength + 1) {
                this.contact_show = false;
                this.is_find = false;
            }

            let position = this.getCursorPosition();
            for (let i = 0; i < this.to.length; i++) {
                let string =
                    '<b><span style="color:green;">' +
                    this.to[i].name +
                    "</span></b>&nbsp";

                let indexText = text.indexOf(this.to[i].name);
                let index = message.indexOf(string);
                let res = position - this.to[i].name.length;

                if (
                    indexText == position - this.to[i].name.length - 1 ||
                    indexText == position - this.to[i].name.length
                ) {
                    mess.innerHTML =
                        message.slice(0, index - 1) +
                        message.slice(
                            index + 1 + string.length,
                            message.length
                        );
                    this.to.splice(i, 1);
                    this.setCursorPosition(mess, position);
                }
            }
            console.log(this.to);
        },
        attachmentShow(attachment) {
            this.show_attach = true;
            let img = document.getElementById("attachment_img");

            this.img_path = "/storage/files/" + this.app_id + "/" + attachment;

            this.$refs.attachment_img.src =
                "/storage/files/" + this.app_id + "/" + attachment;
        },
        getCursorPosition() {
            let selection = window.getSelection();
            let range = new Range();
            let mess = document.getElementById("message_");
            range.setStart(mess, 0);
            range.setEnd(selection.anchorNode, selection.anchorOffset);
            return range.toString().length;
        },
        setCursorPosition(parent, position) {
            let child = parent.firstChild;
            while (position > 0) {
                let length = child.textContent.length;
                if (position > length) {
                    position -= length;
                    child = child.nextSibling;
                } else {
                    if (child.nodeType == 3)
                        return document
                            .getSelection()
                            .collapse(child, position);
                    child = child.firstChild;
                }
            }
        },
        sendMessage() {
            this.can_send = false;
            let mess = document.getElementById("message_");
            let reply = document.getElementById("reply_");

            this.message = mess.innerHTML;

            mess.innerHTML = "";
            let to = [];
            for (let i = 0; i < this.to.length; i++) {
                to.push(this.to[i].id);
            }
            for (let i = 0; i < this.work_group_selected.length; i++) {
                for (let j = 0; j < this.users.length; j++) {
                    if (
                        this.users[j].id == this.work_group_selected[i] &&
                        !to.includes(this.work_group_selected[i]) &&
                        this.work_group_selected[i] != this.user_id
                    ) {
                        to.push(this.work_group_selected[i]);
                        let string =
                            '@<b><span style="color:green;">' +
                            this.users[j].name +
                            "</span></b>&nbsp<br/>";
                        mess.innerHTML = string + mess.innerHTML;
                    }
                }
            }
            mess.innerHTML += this.message;
            this.message = mess.innerHTML;
            if (this.replayShow) {
                this.message = reply.innerHTML + "<br>" + this.message;
                this.replayShow = false;
                reply.innerHTML = "";
            }

            if (this.message.trim == "") return;
            else {
                let formData = new FormData();
                for (let i = 0; i < to.length; i++) {
                    formData.append("to[" + i + "]", to[i]);
                }

                formData.append("message", this.message);
                formData.append("id_application", this.app_id);
                formData.append("from", this.user_id);

                for (let i = 0; i < this.files.length; i++) {
                    let file = this.files[i];
                    formData.append("files[" + i + "]", file);
                }

                axios
                    .post(`/api/chat/send`, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })

                    .then((res) => {
                        this.can_send = true;
                        this.chats.unshift(res.data);

                        mess.innerHTML = "";
                        this.to = [];
                        this.message = "";
                        this.files = [];
                    })
                    .catch((error) => {
                        this.can_send = true;
                    });
            }
        },
        handleFilesUpload() {
            let uploadedFiles = this.$refs.files.files;
            for (var i = 0; i < uploadedFiles.length; i++) {
                this.files.push(uploadedFiles[i]);
            }
        },

        showChat() {
            this.show = !this.show;
            axios
                .post(`/api/chat/readed`, {
                    id_application: this.app_id,
                    user_id: this.user_id,
                })
                .then((res) => {
                    this.count.chat = 0;
                });
        },
        unreaded() {
            axios
                .post(`/api/chat/unreaded`, {
                    id_application: this.app_id,
                    user_id: this.user_id,
                })
                .then((res) => {
                    this.count.chat = this.count.chat + 1;
                });
        },
        findContact() {
            if (this.is_find) {
                let mess = document.getElementById("message_");
                let message = mess.innerText;
                let value = mess.innerText.substring(
                    this.dotLength,
                    message.length
                );
                let regPhrase = new RegExp(value, "i");
                let flag = false;
                for (let i = 0; i < this.users.length; i++) {
                    flag = false;
                    let div = document.getElementById("find_" + i);
                    flag = regPhrase.test(div.innerText);
                    if (flag) {
                        div.style.display = "";
                    } else {
                        div.style.display = "none";
                    }
                }
            }
        },
        answerShow(id) {
            let answer = document.getElementById("answer_" + id);
            if (answer != null) {
                answer.style.display = "block";
            }
        },
        answerHide(id) {
            let answer = document.getElementById("answer_" + id);
            if (answer != null) {
                answer.style.display = "none";
            }
        },
        answerReply(from, from_name, message, time) {
            this.replayShow = true;
            this.to.push({ id: from, name: from_name });
            let mess = document.getElementById("reply_");
            this.reply =
                '<div class="reply"><div class="from">' +
                from_name +
                "</div>" +
                message +
                "<br/>__________________________</div>&nbsp";
            mess.innerHTML = this.reply;
            mess = document.getElementById("message_");
            mess.focus();
            const range = document.createRange();
            range.selectNodeContents(mess);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        },
        noReply() {
            let mess = document.getElementById("reply_");
            mess.innerHTML = "";
            this.replayShow = false;
            this.to.splice(0, 1);
            console.log(this.to);
        },
        removeFile(key) {
            this.files.splice(key, 1);
        },
    },
};
</script>
