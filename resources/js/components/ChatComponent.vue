<template>
    <div class="row">
        <div class="col-md-7">
            <div class="card direct-chat direct-chat-primary" v-if="chatWith">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">Chat With <span class="badge badge-info">{{ chatWith.name }}</span></h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="3 New Messages" class="badge badge-primary">3</span>
                        <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts"
                                data-widget="chat-pane-toggle">
                            <i class="fas fa-comments"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="direct-chat-messages" id="your_div">
                        <component :key="Math.random().toString()" v-for="message in messageList"
                                   :is="message.componentName"
                                   :data="message.body">

                        </component>
                    </div>

                    <div class="direct-chat-contacts">
                        <ul class="contacts-list">
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user1-128x128.jpg">

                                    <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                            Count Dracula
                                            <small class="contacts-list-date float-right">2/28/2015</small>
                                        </span>
                                        <span class="contacts-list-msg">How have you been? I was...</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user1-128x128.jpg">

                                    <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                            Sarah Doe
                                            <small class="contacts-list-date float-right">2/23/2015</small>
                                            </span>
                                        <span class="contacts-list-msg">I will be waiting for...</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user3-128x128.jpg">

                                    <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                            Nadia Jolie
                                            <small class="contacts-list-date float-right">2/20/2015</small>
                                        </span>
                                        <span class="contacts-list-msg">I'll call you back at...</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user1-128x128.jpg">

                                    <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                            Nora S. Vans
                                            <small class="contacts-list-date float-right">2/10/2015</small>
                                        </span>
                                        <span class="contacts-list-msg">Where is your new...</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user1-128x128.jpg">

                                    <div class="contacts-list-info">
                                        <span class="contacts-list-name">
                                            John K.
                                        <small class="contacts-list-date float-right">1/27/2015</small>
                                    </span>
                                        <span class="contacts-list-msg">Can I take a look at...</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="images/user1-128x128.jpg">

                                    <div class="contacts-list-info">
                                    <span class="contacts-list-name">
                                        Kenneth M.
                                        <small class="contacts-list-date float-right">1/4/2015</small>
                                    </span>
                                        <span class="contacts-list-msg">Never mind I found...</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer" style="display: block;">
                    <form action="#" @submit.prevent="sendMessage">
                        <div class="input-group">
                            <input type="text" v-model="messageContent" placeholder="Type Message ..."
                                   class="form-control">
                            <span class="input-group-append">
                      <button type="submit" class="btn btn-primary">
                          Send <i class="fas fa-paper-plane"></i>
                      </button>
                    </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active">
                    Users
                </a>
                <button v-for="user in users" @click="selectUser(user)" href="#"
                        class="list-group-item list-group-item-action">
                    {{ user.name}}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import myMessage from "./myMessage";
    import guestMessage from "./guestMessage";
    import axios from 'axios'

    export default {
        components: {
            myMessage, guestMessage
        },
        props: ['auth'],
        data() {
            return {
                messageList: [], // { body , componentName }
                messageContent: "",
                users: [],
                chatWith: null, // user
            }
        },
        mounted() {
            this.scrollToBottom()
            this.getUsers()
        },
        updated() {
            this.scrollToBottom()
        },
        methods: {
            sendMessage() {
                if (!this.messageContent) return
                this.messageList.push({
                    body: {
                        content: this.messageContent,
                        created_at: this.current_date,
                        userName: this.auth.name
                    },
                    componentName: 'myMessage',
                })
                axios.post(`message/store/${this.chatWith.id}`, {content: this.messageContent});
                this.messageContent = ""

            },
            scrollToBottom() {
                if ($(".direct-chat-messages")[0])
                    $('.direct-chat-messages').scrollTop($(".direct-chat-messages")[0].scrollHeight);
            },
            getUsers() {
                axios.get('/user').then(response => {
                    this.users = response.data;
                })
            },
            selectUser(user) {
                this.chatWith = user;
                axios.get(`user/messages_between/${user.id}`).then(response => {
                    this.messageList = []
                    response.data.forEach(message_i => {
                        this.messageList.push({
                            body: {
                                content: message_i.content,
                                created_at: message_i.created_at,
                                userName: "Some Name",
                            },
                            componentName: message_i.sender_id == this.auth.id ? 'myMessage' : 'guestMessage',
                        })
                    })
                })
            },
        },
        computed: {
            current_date() {
                let today = new Date();
                let date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
                let time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                return date + ' ' + time;
            }
        }
    }
</script>
