app.component('accept-terms', {
    template: $TEMPLATES['accept-terms'],
    emits: [],

    data() {
        const global = useGlobalState();
        const terms = $MAPAS.config.LGPD;
        const accepteds = $MAPAS.hashAccepteds;
        const step = this.getStep();
        const user = global.auth.user;

        return {
            loading: false,
            user,
            terms,
            accepteds,
            step,
        };
    },

    methods: {
        formatDate(timestamp) {
            const date = new McDate(new Date(timestamp * 1000));
            return date.date('numeric year') + ' - ' + date.time('numeric');
        },

        acceptTerm(slug, hash) {
            const url = Utils.createUrl('lgpd', 'accept');
            const api = new API();
            this.loading = true;

            api.POST(url, [slug]).then(res => res.json()).then(data => {
                this.accepteds.push(hash);

                const nextTerm = Object.entries(this.terms).find(([, term]) => {
                    return !this.accepteds.includes(term.md5);
                });

                if (nextTerm) {
                    // A API não devolve os metadados atualizados do usuário.
                    // Recarregar a próxima etapa atualiza o comprovante do aceite anterior.
                    window.location.hash = nextTerm[0];
                    window.location.reload();
                    return;
                }

                window.location.href = data.redirect;
            }).catch(() => {
                this.loading = false;
            });
        },

        showButton(hash) {
            return !this.accepteds.includes(hash);
        },

        showIconAccepted(hash) {
            if (this.accepteds.includes(hash)) {
                return 'circle-checked';
            }
        },

        getStep() {
            const match = window.location.hash.match(/^#([a-zA-Z]{1,61}[0-9]?)$/);
            return match ? match[1] : undefined;
        },
    },
});
