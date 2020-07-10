<template>
    <div class="row">
        <div class="col-md-7">
            <div class="card direct-chat direct-chat-primary" v-if="chatWith">
                <div class="card-header ui-sortable-handle">
                    <h3 class="card-title">Chat With <span class="badge badge-info">{{ chatWith.name }}</span></h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="direct-chat-messages" id="your_div">
                        <component :key="Math.random().toString()" v-for="message in messageList"
                                   :is="message.componentName"
                                   :data="message.body">

                        </component>
                    </div>


                </div>
                <div class="card-footer" style="display: block;">
                    <form action="#" @submit.prevent="sendMessage">
                        <div class="input-group">
                            <input type="text" v-model="messageContent" @keydown="sendTypingEvent"
                                   placeholder="Type Message ..."
                                   class="form-control">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    Send <i class="fas fa-paper-plane"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    <span class="text-muted" v-if="typingUser">{{ typingUser.name }} is typing...</span>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <ul class="list-group">
                <li href="#" class="list-group-item list-group-item-action bg-danger">
                    Users
                </li>
                <li v-for="user in users" @click="selectUser(user)" href="#"
                    :class="{ 'list-group-item':true ,'list-group-item-action':true,'active':user.selected }">
                    {{ user.name }}
                    <span v-if="activeUsers.map(u => u.id).includes(user.id)" class="badge badge-success badge-pill">active</span>
                    <span v-if="user.unseen_msg_cnt && user.unseen_msg_cnt > 0" class="badge-danger badge float-right">{{ user.unseen_msg_cnt }}</span>
                </li>
            </ul>
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
                activeUsers: [],
                notificationsSounds: ['slack.mp3', 'messenger.mp3', 'sound1.mp3'],
                typingUser: null,
                isTyping: false,
            }
        },
        mounted() {
            this.scrollToBottom()
            this.getUsers()

            window.Echo.join(`App.User.${this.auth.id}`).listen('SendMessage', (event) => {
                if (this.chatWith && event.message.sender_id == this.chatWith.id) {
                    // User A , B , C -- if B send to A # B -> A
                    // it will appear that C and B send the same message to A # B -> A & C -> A
                    // this if statement avoid this # only B -> A
                    this.messageList.push({
                        body: {
                            content: event.message.content,
                            created_at: event.message.created_at,
                            userName: 'this.auth.name'
                        },
                        componentName: 'guestMessage',
                    })
                }
                this.incrementUnseenMessagesCount(event.message.sender_id)
                this.fireNotification()
            }).listenForWhisper('typing', user => {
                this.typingUser = user;

                setTimeout(() => {
                    this.typingUser =  null;

                }, 1500);
            })

            window.Echo.join('chat')
                .here(activeUsers => {
                    this.activeUsers = activeUsers
                })
                .joining(user => {
                    this.activeUsers.push(user)
                })
                .leaving(user => {
                    this.activeUsers = this.activeUsers.filter(u => u.id != user.id);
                })


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
                this.markMessagesSeen(this.chatWith)
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

                // get messages between auth and user
                axios.get(`user/messages_between/${user.id}`).then(response => {
                    this.messageList = []
                    response.data.forEach(message_i => {
                        this.messageList.push({
                            body: {
                                content: message_i.content,
                                created_at: message_i.created_at,
                                userName: message_i.sender_id == this.auth.id ? this.auth.name : this.chatWith.name,
                            },
                            componentName: message_i.sender_id == this.auth.id ? 'myMessage' : 'guestMessage',
                        })
                    })
                })

                // mark the selected user
                this.users.forEach((user_i, index) => {
                    if (user_i.id == user.id)
                        this.users[index].selected = true
                    else
                        this.users[index].selected = false
                })

                this.markMessagesSeen(user)
            },
            markMessagesSeen(user) {
                this.users.every((user_i, index) => {
                    if (user_i.id == user.id) {
                        this.users[index].unseen_msg_cnt = 0
                        return false
                    }
                    return true
                })
            },
            incrementUnseenMessagesCount(sender_id) {
                this.users.forEach((user_i, index) => {
                    if (user_i.id == sender_id) {
                        this.users[index].unseen_msg_cnt++
                        return false
                    }
                    return true
                })
            },
            sendTypingEvent() {
                Echo.join(`App.User.${this.chatWith.id}`)
                    .whisper('typing', this.auth);
            },
            fireNotification() {
                const sound = this.notificationsSounds[Math.floor(Math.random() * this.notificationsSounds.length)];
                let src = `/sounds/slack.mp3`;
                let audio = new Audio(src);
                audio.play();
            }
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
