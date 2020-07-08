<template>
    <div class="direct-chat-msg">
        <div class="direct-chat-infos clearfix">
            <span class="direct-chat-name float-left">{{ data.userName }}</span>
            <span class="direct-chat-timestamp float-right">{{ data.created_at }}</span>
        </div>
        <img class="direct-chat-img" src="images/user1-128x128.jpg"
             alt="message user image">
        <div class="direct-chat-text">
            {{ data.content }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "guestMessage",
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

<style scoped>

</style>
