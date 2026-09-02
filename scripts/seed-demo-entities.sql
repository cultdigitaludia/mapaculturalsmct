BEGIN;

DO $seed$
DECLARE
    i integer;
    entity_id integer;
    project_id integer;
    event_date date;
    demo_names text[] := ARRAY[
        'Horizonte Cultural',
        'Circuito das Artes',
        'Encontro de Saberes',
        'Movimento Criativo',
        'Território em Cena',
        'Conexão Cultural'
    ];
BEGIN
    FOR i IN 1..6 LOOP
        SELECT object_id INTO entity_id
          FROM agent_meta
         WHERE key = 'demoSeedKey' AND value = 'agent-' || i
         LIMIT 1;

        IF entity_id IS NULL THEN
            INSERT INTO agent (
                user_id, type, name, location, short_description, long_description,
                create_timestamp, update_timestamp, status, is_verified, public_location
            ) VALUES (
                1, CASE WHEN i % 2 = 0 THEN 2 ELSE 1 END,
                'Agente Demonstração — ' || demo_names[i], point(-48.2772, -18.9186),
                'Agente cultural criado para demonstração e validação da plataforma.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Este cadastro apresenta informações genéricas para testes de navegação, busca e visualização.',
                now() - (i || ' days')::interval, now(), 1, FALSE, TRUE
            ) RETURNING id INTO entity_id;

            INSERT INTO agent_meta (object_id, key, value) VALUES
                (entity_id, 'demoSeedKey', 'agent-' || i),
                (entity_id, 'emailPublico', 'agente' || i || '@exemplo.com.br'),
                (entity_id, 'telefonePublico', '(34) 3000-00' || lpad(i::text, 2, '0'));
        END IF;

        SELECT object_id INTO entity_id
          FROM space_meta
         WHERE key = 'demoSeedKey' AND value = 'space-' || i
         LIMIT 1;

        IF entity_id IS NULL THEN
            INSERT INTO space (
                location, _geo_location, name, short_description, long_description,
                create_timestamp, update_timestamp, status, type, agent_id,
                is_verified, public
            ) VALUES (
                point(-48.2772 + i * 0.002, -18.9186 + i * 0.002),
                ST_SetSRID(ST_MakePoint(-48.2772 + i * 0.002, -18.9186 + i * 0.002), 4326),
                'Espaço Demonstração — ' || demo_names[i],
                'Espaço cultural genérico para demonstração das funcionalidades.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ambiente destinado a atividades culturais, encontros, oficinas e apresentações.',
                now() - (i || ' days')::interval, now(), 1, 21, 1, FALSE, TRUE
            ) RETURNING id INTO entity_id;

            INSERT INTO space_meta (object_id, key, value) VALUES
                (entity_id, 'demoSeedKey', 'space-' || i),
                (entity_id, 'address', 'Rua de Demonstração, ' || (100 + i) || ' - Centro - Uberlândia/MG'),
                (entity_id, 'En_Municipio', 'Uberlândia'),
                (entity_id, 'En_Estado', 'MG'),
                (entity_id, 'acessibilidade', CASE WHEN i % 2 = 0 THEN 'Sim' ELSE 'Não informado' END);
        END IF;

        SELECT object_id INTO project_id
          FROM project_meta
         WHERE key = 'demoSeedKey' AND value = 'project-' || i
         LIMIT 1;

        IF project_id IS NULL THEN
            INSERT INTO project (
                name, short_description, long_description, create_timestamp,
                update_timestamp, status, agent_id, is_verified, type,
                starts_on, ends_on
            ) VALUES (
                'Projeto Demonstração — ' || demo_names[i],
                'Projeto genérico para apresentação e testes da plataforma.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. O projeto promove ações formativas, circulação cultural e participação comunitária.',
                now() - (i || ' days')::interval, now(), 1, 1, FALSE, 1,
                current_date, current_date + 180
            ) RETURNING id INTO project_id;

            INSERT INTO project_meta (object_id, key, value)
            VALUES (project_id, 'demoSeedKey', 'project-' || i);
        END IF;

        SELECT object_id INTO entity_id
          FROM opportunity_meta
         WHERE key = 'demoSeedKey' AND value = 'opportunity-' || i
         LIMIT 1;

        IF entity_id IS NULL THEN
            INSERT INTO opportunity (
                agent_id, type, name, short_description, long_description,
                registration_from, registration_to, published_registrations,
                create_timestamp, update_timestamp, status, object_type, object_id,
                publish_timestamp, auto_publish, publicity_only
            ) VALUES (
                1, 1, 'Oportunidade Demonstração — ' || demo_names[i],
                'Oportunidade aberta para participação de agentes culturais.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Consulte as condições, organize a documentação e envie sua proposta dentro do prazo.',
                now() - interval '1 day', now() + ((30 + i) || ' days')::interval,
                FALSE, now() - (i || ' days')::interval, now(), 1,
                'MapasCulturais\Entities\Project', project_id, now(), FALSE, FALSE
            ) RETURNING id INTO entity_id;

            INSERT INTO opportunity_meta (object_id, key, value)
            VALUES (entity_id, 'demoSeedKey', 'opportunity-' || i);
        END IF;

        SELECT object_id INTO entity_id
          FROM event_meta
         WHERE key = 'demoSeedKey' AND value = 'event-' || i
         LIMIT 1;

        IF entity_id IS NULL THEN
            INSERT INTO event (
                project_id, name, short_description, long_description,
                create_timestamp, update_timestamp, status, agent_id,
                is_verified, type
            ) VALUES (
                project_id, 'Evento Demonstração — ' || demo_names[i],
                'Evento cultural genérico com programação aberta ao público.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. A programação reúne atividades culturais, convivência e experiências para diferentes públicos.',
                now() - (i || ' days')::interval, now(), 1, 1, FALSE, 1
            ) RETURNING id INTO entity_id;

            INSERT INTO event_meta (object_id, key, value)
            VALUES (entity_id, 'demoSeedKey', 'event-' || i);

            event_date := current_date + (i * 7);
            INSERT INTO event_occurrence (
                space_id, event_id, rule, starts_on, starts_at, ends_at,
                frequency, separation, timezone_name, status, description,
                price, priceinfo
            ) VALUES (
                (SELECT object_id FROM space_meta WHERE key = 'demoSeedKey' AND value = 'space-' || i LIMIT 1),
                entity_id,
                json_build_object(
                    'startsOn', event_date::text,
                    'startsAt', '19:00',
                    'endsAt', '21:00',
                    'duration', 120,
                    'frequency', 'once',
                    'description', 'Evento de demonstração das 19h às 21h',
                    'price', 'Gratuito',
                    'spaceId', (SELECT object_id FROM space_meta WHERE key = 'demoSeedKey' AND value = 'space-' || i LIMIT 1)
                )::text,
                event_date,
                event_date::timestamp + time '19:00',
                event_date::timestamp + time '21:00',
                'once', 1, 'America/Sao_Paulo', 1,
                'Evento de demonstração das 19h às 21h', 'Gratuito', 'Entrada franca'
            );
        END IF;
    END LOOP;
END
$seed$;

UPDATE event_occurrence occurrence
SET rule = (occurrence.rule::jsonb || jsonb_build_object('duration', 120))::text
WHERE NOT (occurrence.rule::jsonb ? 'duration')
  AND EXISTS (
      SELECT 1
      FROM event_meta metadata
      WHERE metadata.object_id = occurrence.event_id
        AND metadata.key = 'demoSeedKey'
        AND metadata.value LIKE 'event-%'
  );

WITH demo_entities AS (
    SELECT object_id, 'MapasCulturais\Entities\Agent'::object_type AS object_type
      FROM agent_meta WHERE key = 'demoSeedKey'
    UNION
    SELECT object_id, 'MapasCulturais\Entities\Space'::object_type
      FROM space_meta WHERE key = 'demoSeedKey'
    UNION
    SELECT object_id, 'MapasCulturais\Entities\Project'::object_type
      FROM project_meta WHERE key = 'demoSeedKey'
    UNION
    SELECT object_id, 'MapasCulturais\Entities\Opportunity'::object_type
      FROM opportunity_meta WHERE key = 'demoSeedKey'
    UNION
    SELECT object_id, 'MapasCulturais\Entities\Event'::object_type
      FROM event_meta WHERE key = 'demoSeedKey'
)
INSERT INTO entity_revision (
    id, user_id, object_id, object_type, create_timestamp, action, message
)
SELECT nextval('entity_revision_id_seq'), 1, demo.object_id, demo.object_type,
       now(), 'created', 'Entidade de demonstração registrada.'
  FROM demo_entities demo
 WHERE NOT EXISTS (
     SELECT 1 FROM entity_revision revision
      WHERE revision.object_type = demo.object_type
        AND revision.object_id = demo.object_id
 );

SELECT setval('agent_id_seq', (SELECT MAX(id) FROM agent));
SELECT setval('space_id_seq', (SELECT MAX(id) FROM space));
SELECT setval('project_id_seq', (SELECT MAX(id) FROM project));
SELECT setval('opportunity_id_seq', (SELECT MAX(id) FROM opportunity));
SELECT setval('event_id_seq', (SELECT MAX(id) FROM event));
SELECT setval('event_occurrence_id_seq', (SELECT MAX(id) FROM event_occurrence));
SELECT setval('entity_revision_id_seq', GREATEST((SELECT MAX(id) FROM entity_revision), 1));

COMMIT;
