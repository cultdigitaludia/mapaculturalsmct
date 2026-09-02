#!/usr/bin/env node

const fs = require('fs');

const sourcePath = process.argv[2];
if (!sourcePath) {
    throw new Error('Informe o arquivo JSON exportado pela API do Culture-se');
}

const spaces = JSON.parse(fs.readFileSync(sourcePath, 'utf8'));
const entityType = 'MapasCulturais\\Entities\\Space';

const quote = (value) => value === null || value === undefined
    ? 'NULL'
    : `'${String(value).replaceAll("'", "''")}'`;

const bool = (value) => value ? 'TRUE' : 'FALSE';

const metadataKeys = [
    'address', 'address_level0', 'address_level2', 'address_level4',
    'address_level6', 'address_line1', 'address_line2', 'address_postalCode',
    'En_CEP', 'En_Nome_Logradouro', 'En_Num', 'En_Complemento', 'En_Bairro',
    'En_Municipio', 'En_Estado', 'endereco', 'emailPublico', 'telefonePublico',
    'acessibilidade', 'capacidade', 'horario', 'site', 'facebook', 'instagram',
    'twitter', 'youtube'
];

console.log('BEGIN;');

for (const space of spaces) {
    const longitude = space.location?.longitude;
    const latitude = space.location?.latitude;
    const location = longitude !== undefined && latitude !== undefined
        ? `point(${Number(longitude)}, ${Number(latitude)})`
        : 'point(0, 0)';
    const geoLocation = longitude !== undefined && latitude !== undefined
        ? `ST_SetSRID(ST_MakePoint(${Number(longitude)}, ${Number(latitude)}), 4326)`
        : 'NULL';

    console.log(`
DO $import$
DECLARE
    imported_id integer;
    selected_term_id integer;
BEGIN
    SELECT object_id INTO imported_id
      FROM space_meta
     WHERE key = 'cultureseSourceId' AND value = ${quote(space.id)}
     LIMIT 1;

    IF imported_id IS NULL THEN
        INSERT INTO space (
            location, _geo_location, name, short_description, long_description,
            create_timestamp, status, type, agent_id, is_verified, public,
            update_timestamp
        ) VALUES (
            ${location}, ${geoLocation}, ${quote(space.name)},
            ${quote(space.shortDescription)}, ${quote(space.longDescription)},
            ${quote(space.createTimestamp?.date)}::timestamp, 1, ${Number(space.type.id)},
            1, FALSE, ${bool(space.public)}, ${quote(space.updateTimestamp?.date)}::timestamp
        ) RETURNING id INTO imported_id;

        INSERT INTO space_meta (object_id, key, value) VALUES
            (imported_id, 'cultureseSourceId', ${quote(space.id)}),
            (imported_id, 'cultureseSourceUrl', ${quote(space.singleUrl)});
    ELSE
        UPDATE space SET
            location = ${location}, _geo_location = ${geoLocation}, name = ${quote(space.name)},
            short_description = ${quote(space.shortDescription)}, long_description = ${quote(space.longDescription)},
            status = 1, type = ${Number(space.type.id)}, public = ${bool(space.public)},
            update_timestamp = ${quote(space.updateTimestamp?.date)}::timestamp
        WHERE id = imported_id;

        DELETE FROM space_meta
         WHERE object_id = imported_id
           AND key NOT IN ('cultureseSourceId', 'cultureseSourceUrl');
    END IF;
`);

    for (const key of metadataKeys) {
        const value = space[key];
        if (value !== null && value !== undefined && !Array.isArray(value) && typeof value !== 'object') {
            console.log(`    INSERT INTO space_meta (object_id, key, value) VALUES (imported_id, ${quote(key)}, ${quote(value)});`);
        }
    }

    console.log(`    DELETE FROM term_relation WHERE object_type = ${quote(entityType)} AND object_id = imported_id;`);

    for (const taxonomy of ['area', 'tag']) {
        for (const term of space.terms?.[taxonomy] || []) {
            console.log(`
    SELECT id INTO selected_term_id FROM term WHERE taxonomy = ${quote(taxonomy)} AND term = ${quote(term)} LIMIT 1;
    IF selected_term_id IS NULL THEN
        INSERT INTO term (taxonomy, term) VALUES (${quote(taxonomy)}, ${quote(term)}) RETURNING id INTO selected_term_id;
    END IF;
    INSERT INTO term_relation (term_id, object_type, object_id) VALUES (selected_term_id, ${quote(entityType)}, imported_id);`);
        }
    }

    console.log(`
    IF NOT EXISTS (
        SELECT 1 FROM entity_revision
         WHERE object_type = ${quote(entityType)}::object_type
           AND object_id = imported_id
    ) THEN
        INSERT INTO entity_revision (
            id, user_id, object_id, object_type, create_timestamp, action, message
        ) VALUES (
            nextval('entity_revision_id_seq'), 1, imported_id,
            ${quote(entityType)}::object_type, now(), 'created',
            'Espaço importado registrado.'
        );
    END IF;`);

    console.log(`END
$import$;`);
}

console.log(`
SELECT setval('space_id_seq', (SELECT MAX(id) FROM space));
SELECT setval('space_meta_id_seq', (SELECT MAX(id) FROM space_meta));
SELECT setval('term_id_seq', (SELECT MAX(id) FROM term));
SELECT setval('term_relation_id_seq', (SELECT MAX(id) FROM term_relation));
SELECT setval('entity_revision_id_seq', GREATEST((SELECT MAX(id) FROM entity_revision), 1));
COMMIT;`);
