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
        const turismoIds = [1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019];
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
            const turismoIds = [1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019];
            for (const key in this.pseudoQuery) {
                if (Array.isArray(this.pseudoQuery[key])) {
                    if (key === 'type') {
                        this.pseudoQuery[key] = turismoIds;
                    } else {
                        this.pseudoQuery[key] = [];
                    }
                } else if (typeof this.pseudoQuery[key] === 'string' || typeof this.pseudoQuery[key] === 'boolean') {
                    delete this.pseudoQuery[key];
                }
            }
        },
    },
});
