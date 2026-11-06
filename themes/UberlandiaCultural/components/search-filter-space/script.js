/**
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferenças: o filtro de tipos oferece só os tipos da
 * seção Espaços (todos menos os de Turismo, definidos em
 * Theme::TURISMO_SPACE_TYPES) e "Limpar todos os filtros" preserva "@secao",
 * que mantém a listagem restrita a esses tipos. Ao atualizar o MapasCulturais,
 * compare este arquivo com a nova versão oficial.
 */
app.component('search-filter-space', {
    template: $TEMPLATES['search-filter-space'],

    setup() {
        // os textos estão localizados no arquivo texts.php deste componente 
        const text = Utils.getTexts('search-filter-space')
        return { text }
    },

    props: {
        position: {
            type: String,
            default: 'list'
        },
        pseudoQuery: {
            type: Object,
            required: true
        }
    },

    data() {
        const turismoIds = ($MAPAS.config.sectionSpaceTypes?.turismo || []).map(String);
        const types = {};
        for (const [value, label] of Object.entries($DESCRIPTIONS.space.type.options)) {
            if (!turismoIds.includes(String(value))) {
                types[value] = label;
            }
        }

        return {
            terms: $TAXONOMIES.area.terms,
            types,
        }
    },

    computed: {
    },

    methods: {
        clearFilters() {
            const types = ['string', 'boolean'];
            for (const key in this.pseudoQuery) {
                if (key === '@secao') {
                    continue;
                }
                if (Array.isArray(this.pseudoQuery[key])) {
                    this.pseudoQuery[key] = [];
                } else if (types.includes(typeof this.pseudoQuery[key])) {
                    delete this.pseudoQuery[key];
                }
            }            
        },
    },
});
