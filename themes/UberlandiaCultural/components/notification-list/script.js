app.component('notification-list', {
    template: $TEMPLATES['notification-list'],

    setup() {
        const text = Utils.getTexts('notification-list');
        return { text };
    },

    created() {
        this.API = new API('notification');
    },

    props: {
        query: {
            type: Object,
            default: {
                '@select': '*,request.{requesterUser.profile.files.avatar}',
                'user': 'eq(@me)',
                '@order': 'createTimestamp DESC'
            }
        },
        styleCss: {
            type: String,
            default: 'card'
        }
    },

    data() {
        const global = useGlobalState();
        return {
            currentUserId: global.auth.user?.id
        };
    },

    methods: {
        messageDocument(message) {
            return new DOMParser().parseFromString(message || '', 'text/html');
        },

        editLink(document) {
            return [...document.querySelectorAll('a')].find((link) =>
                link.textContent.trim().toLocaleLowerCase('pt-BR') === 'editar'
            );
        },

        editUrl(message) {
            const link = this.editLink(this.messageDocument(message));
            return link?.getAttribute('href') || null;
        },

        messageWithoutEdit(message) {
            const document = this.messageDocument(message);
            this.editLink(document)?.remove();
            return document.body.innerHTML.trim();
        },

        hasAvatar(entity) {
            return !!entity.request?.requesterUser?.profile?.files?.avatar;
        },

        avatarUrl(entity) {
            if (this.hasAvatar(entity)) {
                return entity.request?.requesterUser?.profile?.files?.avatar?.transformations?.avatarSmall?.url ||
                    entity.request?.requesterUser?.profile?.files?.avatar?.url;
            }
        },

        async approve(notification) {
            const url = this.API.createUrl('approve', [notification.id]);
            const request = await this.API.POST(url);
            if (request) {
                useMessages().success(this.text('notificacao_aprovada'));
            }
        },

        async reject(notification) {
            const url = this.API.createUrl('reject', [notification.id]);
            const request = await this.API.POST(url);
            if (request) {
                useMessages().success(this.text('notificacao_recusada'));
                notification.removeFromLists();
            }
        },

        async cancel(notification) {
            const url = this.API.createUrl('reject', [notification.id]);
            const request = await this.API.POST(url);
            if (request) {
                useMessages().success(this.text('notificacao_cancelada'));
                notification.removeFromLists();
            }
        },

        async ok(notification) {
            notification.disableMessages();
            await notification.delete();
            notification.removeFromLists();
        }
    }
});
