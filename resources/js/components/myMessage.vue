<template>
    <div class="direct-chat-msg right">
        <div class="direct-chat-infos clearfix">
            <span class="direct-chat-name float-right">{{ data.userName }}</span>
            <span class="direct-chat-timestamp float-left">{{ data.created_at }}</span>
        </div>
        <img class="direct-chat-img" src="images/user3-128x128.jpg"
             alt="message user image">
        <div class="direct-chat-text">
           {{ data.content }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "myMessage",
        props: {
            data: {
                type: Object,
                default: function () {
                    return {
                        content: "this is Message Content",
                        userName: "User Name",
                        created_at: "Created At",
                    }
                }
            }
        }
    }
</script>

