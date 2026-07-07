
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
        const turismoIds = [1000,1001,1002,1003,1006,1007,1008,1010,1011,1016,1017,1018,1019,13,14,20,21,22,23,24,25,26,30,31,40,41,60,61,80,81,82,84,85,100,101,109,113,120,121,123,128,130,135,300,301,302,303,400,401,402,403,601,602,603,604,700,701,702,703,800,801,802,803,804,805,806,807,808,809,810];
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
            const turismoIds = [1000,1001,1002,1003,1006,1007,1008,1010,1011,1016,1017,1018,1019,13,14,20,21,22,23,24,25,26,30,31,40,41,60,61,80,81,82,84,85,100,101,109,113,120,121,123,128,130,135,300,301,302,303,400,401,402,403,601,602,603,604,700,701,702,703,800,801,802,803,804,805,806,807,808,809,810];
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