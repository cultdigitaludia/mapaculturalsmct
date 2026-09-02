app.component('create-account', {
    template: $TEMPLATES['create-account'],

    components: {
        VueRecaptcha
    },

    setup() {
        const text = Utils.getTexts('create-account')
        return { text }
    },

    data() {
        const globalState = useGlobalState();
        const terms = $MAPAS.config.LGPD;
        const termsQtd = Object.entries(terms).length;

        return {
            actualStep: globalState['stepper'] ?? 0,
            totalSteps: termsQtd + 2,
            terms,
            passwordRules: {},
            passwordRulesLoaded: false,
            strongness: 0,
            strongnessClass: 'fraco',
            slugs: [],
            email: '',
            cpf: '',
            documentType: 'cpf',
            fieldErrors: {
                email: [],
                cpf: [],
                password: [],
                confirm_password: [],
                general: [],
            },
            agentFieldErrors: {
                name: '',
                shortDescription: '',
                taxonomies: {},
            },
            password: '',
            confirmPassword: '',
            agent: null,
            recaptchaResponse: '',
            created: false,
            emailSent: false,
        }
    },

    props: {
        config: {
            type: String,
            required: true
        }
    },

    mounted() {
        let api = new API();
        api.GET($MAPAS.baseURL + "auth/passwordvalidationinfos").then(async response => response.json().then(validations => {
            this.passwordRules = validations.passwordRules;
            this.passwordRulesLoaded = true;
        }));
    },

    destroyed() {
        window.removeEventListener('scroll');
    },

    watch: {
        agent: {
            deep: true,
            handler(agent) {
                if (!agent) return;

                if (agent.name?.trim()) {
                    this.agentFieldErrors.name = '';
                }

                if (agent.shortDescription?.trim()) {
                    this.agentFieldErrors.shortDescription = '';
                }

                for (const taxonomy of Object.keys(this.agentFieldErrors.taxonomies)) {
                    if (agent.terms?.[taxonomy]?.length) {
                        this.agentFieldErrors.taxonomies[taxonomy] = '';
                    }
                }
            }
        }
    },

    computed: {
        arraySteps() {
            let steps = Object.entries(this.terms).length + 2;
            let totalSteps = [];
            for (let i = 0; i < steps; i++) {
                totalSteps.push(i);
            }
            return totalSteps;
        },

        step() {
            return this.actualStep;
        },

        configs() {
            return JSON.parse(this.config);
        },

        passwordCriteria() {
            if (!this.passwordRulesLoaded) {
                return [];
            }

            const password = this.password || '';
            const rules = this.passwordRules;
            const minimumLength = Number(rules.minimumPasswordLength || 8);
            const criteria = [{
                label: this.text('{num} caracteres').replace('{num}', minimumLength),
                met: password.length >= minimumLength
            }];

            if (rules.passwordMustHaveNumbers) {
                criteria.push({ label: this.text('um número'), met: /[0-9]/.test(password) });
            }

            if (rules.passwordMustHaveSpecialCharacters) {
                criteria.push({ label: this.text('um caractere especial'), met: /['^£$%&*()}{@#~?><>,|=_"!¨+`´[\].;:/-]/.test(password) });
            }

            if (rules.passwordMustHaveCapitalLetters) {
                criteria.push({ label: this.text('uma letra maiúscula'), met: /[A-Z]/.test(password) });
            }

            if (rules.passwordMustHaveLowercaseLetters) {
                criteria.push({ label: this.text('uma letra minúscula'), met: /[a-z]/.test(password) });
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
        },

        passwordStrongness() {
            if (this.password) {
                let passwordMustHaveCapitalLetters = /[A-Z]/;
                let passwordMustHaveLowercaseLetters = /[a-z]/;
                let passwordMustHaveSpecialCharacters = /[$@$!%*#?&\.\,\:<>+\_\-\"\'()]/;
                let passwordMustHaveNumbers = /[0-9]/;
                let minimumPasswordLength = 8;
                let pwd = this.password;
                let rules = [];

                if (this.passwordRules.passwordMustHaveCapitalLetters) {
                    rules.push(passwordMustHaveCapitalLetters);
                }

                if (this.passwordRules.passwordMustHaveLowercaseLetters) {
                    rules.push(passwordMustHaveLowercaseLetters);
                }

                if (this.passwordRules.passwordMustHaveSpecialCharacters) {
                    rules.push(passwordMustHaveSpecialCharacters);
                }

                if (this.passwordRules.passwordMustHaveNumbers) {
                    rules.push(passwordMustHaveNumbers);
                }

                if (this.passwordRules.minimumPasswordLength) {
                    minimumPasswordLength = this.passwordRules.minimumPasswordLength
                }

                let rulesLength = rules.length;
                let prog = rules.reduce(function (accumulator, test) {
                    return accumulator + (test.test(pwd) ? 1 : 0);
                }, 0);

                let percentToAdd = 100 / (rulesLength + 1);
                let currentPercent = prog * 100 / (rulesLength + 1);

                if (pwd.length > minimumPasswordLength - 1) {
                    currentPercent = currentPercent + percentToAdd;
                }

                let strongness = currentPercent.toFixed(0)
                if (strongness >= 0 && strongness <= 40) {
                    this.strongnessClass = 'fraco';
                }
                if (strongness >= 40 && strongness <= 90) {
                    this.strongnessClass = 'medio';
                }
                if (strongness >= 90 && strongness <= 100) {
                    this.strongnessClass = 'forte';
                }

                return currentPercent.toFixed(0);
            } else {
                return 0;
            }

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

        startAgent() {
            this.agent = Vue.ref(new Entity('agent'));
            this.agent.type = this.documentType === 'cnpj' ? 2 : 1;
            this.agent.terms = { area: [] }
        },

        async nextStep() {
            this.goToStep(this.actualStep + 1);
        },

        previousStep() {
            this.goToStep(this.actualStep - 1);
        },

        async goToStep(step) {
            const globalState = useGlobalState();

            if (this.actualStep == 0) {
                if (await this.validateFields()) {
                    this.actualStep = step;
                    if (step == this.totalSteps - 1) {
                        this.startAgent();
                    }
                }
            } else {
                if (step == this.totalSteps - 1) {
                    this.startAgent();
                }
                this.actualStep = step;
            }

            if (this.actualStep >= this.totalSteps) {
                this.actualStep = this.totalSteps;
            } else if (this.actualStep <= 0) {
                this.actualStep = 0;
            }

            globalState['stepper'] = this.actualStep;
            window.scrollTo(0, 0);
        },

        /* Terms */
        acceptTerm(slug) {
            this.slugs.push(slug);
        },

        /* Do register */
        async register() {
            let api = new API();

            if (this.validateAgent()) {
                let dataPost = {
                    'name': this.agent.name,
                    'email': this.email,
                    'cpf': this.cpf,
                    'document_type': this.documentType,
                    'password': this.password,
                    'confirm_password': this.confirmPassword,
                    'slugs': this.slugs,
                    'g-recaptcha-response': this.recaptchaResponse,
                    'agentData': {
                        'name': this.agent.name,
                        'terms:area': this.agent.terms.area,
                        'shortDescription': this.agent.shortDescription,
                        'type': this.agent.type,
                    },
                }

                await api.POST($MAPAS.baseURL + "autenticacao/register", dataPost).then(response => response.json().then(dataReturn => {
                    if (dataReturn.error) {
                        this.throwErrors(dataReturn.data);
                    } else {
                        if (dataReturn.redirectTo) {
                            window.location = dataReturn.redirectTo;
                        }
                        this.created = true;
                        if (dataReturn.emailSent) {
                            this.emailSent = true;
                        }
                        window.scrollTo(0, 0);
                    }
                }));
            }
        },

        /* Cancel register */
        cancel() {
            this.strongnessClass = 'fraco';
            this.email = '';
            this.cpf = '';
            this.password = '';
            this.confirmPassword = '';
            this.agent = null;
            this.slugs = [];
            this.goToStep(0);
        },

        /* Validações */

        async verifyCaptcha(response) {
            this.recaptchaResponse = response;
        },

        expiredCaptcha() {
            this.recaptchaResponse = '';
        },

        async validateFields() {
            let api = new API();
            let success = true;
            this.clearFieldErrors();
            let data = {
                'cpf': this.cpf,
                'document_type': this.documentType,
                'email': this.email,
                'password': this.password,
                'confirm_password': this.confirmPassword,
                'g-recaptcha-response': this.recaptchaResponse,
            }
            await api.POST($MAPAS.baseURL + "autenticacao/validate", data).then(response => response.json().then(dataReturn => {
                if (dataReturn.error) {
                    this.setFieldErrors(dataReturn.data);
                    success = false;
                } else {
                    this.recaptchaResponse = '';
                }
            }));

            return success;
        },

        throwErrors(errors) {
            const messages = useMessages();

            if (this.recaptchaResponse !== '') {
                grecaptcha.reset();
                this.expiredCaptcha();
            }

            for (let key in errors) {
                for (const message of this.flattenErrorMessages(errors[key])) {
                    messages.error(message);
                }
            }
        },

        setFieldErrors(errors) {
            if (this.recaptchaResponse !== '') {
                grecaptcha.reset();
                this.expiredCaptcha();
            }

            this.mapFieldErrors(errors);
        },

        mapFieldErrors(errors, parentField = null) {
            const aliases = {
                confirmPassword: 'confirm_password',
                'g-recaptcha-response': 'general',
                recaptcha: 'general',
            };

            if (Array.isArray(errors)) {
                const field = parentField || 'general';
                const messages = this.flattenErrorMessages(errors)
                    .map((message) => this.formatFieldError(message, field));
                this.fieldErrors[field].push(...messages);
                return;
            }

            if (errors && typeof errors === 'object') {
                for (const [key, value] of Object.entries(errors)) {
                    const field = aliases[key] || (this.fieldErrors[key] ? key : null);
                    this.mapFieldErrors(value, field || parentField);
                }
                return;
            }

            if (errors) {
                const field = parentField || 'general';
                this.fieldErrors[field].push(this.formatFieldError(String(errors), field));
            }
        },

        formatFieldError(message, field) {
            if (field === 'cpf' && this.documentType === 'cnpj') {
                return message.replace(/cpf/gi, 'CNPJ');
            }

            return message;
        },

        flattenErrorMessages(error) {
            if (Array.isArray(error)) {
                return error.flatMap((item) => this.flattenErrorMessages(item));
            }

            if (error && typeof error === 'object') {
                return Object.values(error).flatMap((item) => this.flattenErrorMessages(item));
            }

            return error ? [String(error)] : [];
        },

        clearFieldError(field) {
            this.fieldErrors[field] = [];
        },

        changeDocumentType() {
            this.cpf = '';
            this.clearFieldError('cpf');
        },

        formatCnpj(event) {
            const value = event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            const base = value.slice(0, 12);
            const digits = value.slice(12).replace(/[^0-9]/g, '').slice(0, 2);
            const normalized = base + digits;
            let formatted = normalized.slice(0, 2);

            if (normalized.length > 2) formatted += `.${normalized.slice(2, 5)}`;
            if (normalized.length > 5) formatted += `.${normalized.slice(5, 8)}`;
            if (normalized.length > 8) formatted += `/${normalized.slice(8, 12)}`;
            if (normalized.length > 12) formatted += `-${normalized.slice(12, 14)}`;

            this.cpf = formatted;
            event.target.value = formatted;
        },

        clearFieldErrors() {
            Object.keys(this.fieldErrors).forEach((field) => {
                this.fieldErrors[field] = [];
            });
        },

        validateAgent() {
            this.resetAgentFieldErrors();
            let hasErrors = false;

            if (!this.agent.name?.trim()) {
                this.agentFieldErrors.name = __('O nome é obrigatório!', 'create-account');
                hasErrors = true;
            }

            if (!this.agent.shortDescription?.trim()) {
                this.agentFieldErrors.shortDescription = __('A descrição é obrigatória!', 'create-account');
                hasErrors = true;
            }

            Object.keys($TAXONOMIES).forEach(taxonomy => {
                const t = $TAXONOMIES[taxonomy];
                if (t.required  && t.entities.includes('MapasCulturais\\Entities\\Agent')) {
                    if (!this.agent.terms?.[taxonomy]?.length) {
                        const message = taxonomy === 'area'
                            ? __('A área de atuação é obrigatória!', 'create-account')
                            : `${t.description} ${__('required', 'create-account')}`;
                        this.agentFieldErrors.taxonomies[taxonomy] = message;
                        hasErrors = true;
                    }
                }
            });

            return !hasErrors;
        },

        resetAgentFieldErrors() {
            this.agentFieldErrors.name = '';
            this.agentFieldErrors.shortDescription = '';
            this.agentFieldErrors.taxonomies = {};
        },

        togglePassword(id, event) {
            if (document.getElementById(id).type == 'password') {
                event.target.style.background = "url('https://api.iconify.design/carbon/view-off-filled.svg') no-repeat center center / 22.5px"
                document.getElementById(id).type = 'text';
            } else {
                event.target.style.background = "url('https://api.iconify.design/carbon/view-filled.svg') no-repeat center center / 22.5px"
                document.getElementById(id).type = 'password';
            }
        },
    },
});
