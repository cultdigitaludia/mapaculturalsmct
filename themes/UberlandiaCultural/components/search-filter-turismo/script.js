
app.component('search-filter-turismo', {
    template: $TEMPLATES['search-filter-turismo'],
    setup() {
        const text = Utils.getTexts('search-filter-turismo')
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
        // Tipos da seção Turismo, definidos em Theme::TURISMO_SPACE_TYPES.
        const turismoIds = ($MAPAS.config.sectionSpaceTypes?.turismo || []).map(Number);
        const allTypes = $DESCRIPTIONS.space.type.options;
        
        // options pode ser objeto ou array
        let types = [];
        if (Array.isArray(allTypes)) {
            types = allTypes.filter(t => turismoIds.includes(parseInt(t.value)));
        } else if (typeof allTypes === 'object') {
            types = Object.entries(allTypes)
                .filter(([k]) => turismoIds.includes(parseInt(k)))
                .map(([k, v]) => ({ value: k, label: v }));
        }
        return { types };
    },
    methods: {
        clearFilters() {
            // "@secao" mantém a listagem restrita aos tipos de Turismo.
            for (const key in this.pseudoQuery) {
                if (key === '@secao') {
                    continue;
                }
                if (Array.isArray(this.pseudoQuery[key])) {
                    this.pseudoQuery[key] = [];
                } else if (typeof this.pseudoQuery[key] === 'string' || typeof this.pseudoQuery[key] === 'boolean') {
                    delete this.pseudoQuery[key];
                }
            }
        },
    },
});