app.component('change-password', {
    template: $TEMPLATES['change-password'],

    components: {
        VueRecaptcha
    },

    setup() {
        const messages = useMessages();
        const text = Utils.getTexts('change-password');
        return { text, messages };
    },

    data() {
        return {
            passwordRules: {},
            passwordRulesLoaded: false,
            currentPassword: null,
            newPassword: null,
            confirmNewPassword: null
        };
    },

    props: {
        entity: {
            type: Entity,
            required: true
        },
        myAccount: {
            type: Boolean,
            required: false
        },
    },

    mounted() {
        const api = new API();
        api.GET($MAPAS.baseURL + 'auth/passwordvalidationinfos').then(async response => response.json().then(validations => {
            this.passwordRules = validations.passwordRules;
            this.passwordRulesLoaded = true;
        }));
    },

    computed: {
        passwordCriteria() {
            if (!this.passwordRulesLoaded) {
                return [];
            }

            const password = this.newPassword || '';
            const rules = this.passwordRules;
            const minimumLength = Number(rules.minimumPasswordLength || 6);
            const criteria = [{
                key: 'minimum-length',
                label: this.text('{num} caracteres').replace('{num}', minimumLength),
                met: password.length >= minimumLength
            }];

            if (rules.passwordMustHaveNumbers) {
                criteria.push({
                    key: 'number',
                    label: this.text('um número'),
                    met: /[0-9]/.test(password)
                });
            }

            if (rules.passwordMustHaveSpecialCharacters) {
                criteria.push({
                    key: 'special-character',
                    label: this.text('um caractere especial'),
                    met: /['^£$%&*()}{@#~?><>,|=_"!¨+`´[\].;:/-]/.test(password)
                });
            }

            if (rules.passwordMustHaveCapitalLetters) {
                criteria.push({
                    key: 'capital-letter',
                    label: this.text('uma letra maiúscula'),
                    met: /[A-Z]/.test(password)
                });
            }

            if (rules.passwordMustHaveLowercaseLetters) {
                criteria.push({
                    key: 'lowercase-letter',
                    label: this.text('uma letra minúscula'),
                    met: /[a-z]/.test(password)
                });
            }

            return criteria;
        },

        pendingPasswordCriteria() {
            return this.passwordCriteria.filter((criterion) => !criterion.met);
        },

        pendingPasswordRequirementsText() {
            return this.formatPasswordRequirements(this.pendingPasswordCriteria);
        },

        allPasswordCriteriaMet() {
            return this.passwordCriteria.length > 0
                && this.passwordCriteria.every((criterion) => criterion.met);
        }
    },

    methods: {
        formatPasswordRequirements(criteria) {
            const labels = criteria.map((criterion) => criterion.label);

            if (labels.length <= 1) {
                return labels[0] || '';
            }

            return `${labels.slice(0, -1).join(', ')} e ${labels[labels.length - 1]}`;
        },

        async changePassword(modal) {
            const api = new API();
            if (this.myAccount) {
                const data = {
                    'current_password': this.currentPassword,
                    'new_password': this.newPassword,
                    'confirm_new_password': this.confirmNewPassword,
                };
                await api.POST($MAPAS.baseURL + 'autenticacao/changepassword', data).then(response => response.json().then(dataReturn => {
                    if (dataReturn.error) {
                        this.throwErrors(dataReturn.data);
                    } else {
                        this.messages.success('Senha alterada com sucesso!');
                        this.cancel(modal);
                    }
                }));
            } else {
                const data = {
                    'new_password': this.newPassword,
                    'confirm_new_password': this.confirmNewPassword,
                    'email': this.entity.email,
                };
                await api.POST($MAPAS.baseURL + 'autenticacao/adminchangeuserpassword', data).then(response => response.json().then(dataReturn => {
                    if (dataReturn.error) {
                        this.throwErrors(dataReturn.data);
                    } else {
                        this.messages.success('Senha alterada com sucesso!');
                        this.cancel(modal);
                    }
                }));
            }
        },

        cancel(modal) {
            this.currentPassword = '';
            this.newPassword = '';
            this.confirmNewPassword = '';
            modal.close();
        },

        throwErrors(errors) {
            for (const key in errors) {
                for (const val of errors[key]) {
                    this.messages.error(val);
                }
            }
        },

        togglePassword(id, event) {
            if (document.getElementById(id).type === 'password') {
                event.target.style.background = "url('https://api.iconify.design/carbon/view-off-filled.svg') no-repeat center center / 22.5px";
                document.getElementById(id).type = 'text';
            } else {
                event.target.style.background = "url('https://api.iconify.design/carbon/view-filled.svg') no-repeat center center / 22.5px";
                document.getElementById(id).type = 'password';
            }
        },
    },
});
