<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-link
');
?>
<div class="home-entities">
    <div class="home-entities__content">
        <div class="home-entities__content--header">
            <label class="title">
                <?= $this->text('title', i::__('Explore informações sobre cultura e turismo em Uberlândia.')) ?>
            </label>
            <label class="description">
                <?= $this->text('description', i::__('Colabore com o mapeamento cultural da cidade: cadastre seus projetos, espaços, eventos e iniciativas.')) ?>
            </label>
        </div>
        
        <div class="home-entities__content--cards">

            <!-- Turismo -->
            <div class="card card--turismo">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Turismo') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_1.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('turismo', i::__('Explore os pontos turísticos, espaços culturais, gastronomia, meios de hospedagem e muito mais de Uberlândia. <br> Conheça e divulgue o turismo da nossa cidade.')) ?></p>
                        <a href="/search/turismo" class="button button--icon button--sm space__color">
                            <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </a>
                </div>
            </div>

            <!-- Oportunidades -->
            <div v-if="global.enabledEntities.opportunities" class="card card--opportunities">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Oportunidades') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_2.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('opportunities', i::__('Faça a sua inscrição ou acesse o resultado de diversas convocatórias como editais, oficinas, prêmios e concursos. <br> Você também pode criar o seu próprio formulário e divulgar uma oportunidade para outros agentes culturais.')) ?></p>
                    <mc-link route="search/opportunities" class="button button--icon button--sm opportunity__color">
                        <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </mc-link>
                </div>
            </div>

            <!-- Eventos -->
            <div v-if="global.enabledEntities.events" class="card card--events">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Eventos') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_3.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('events', i::__('Você pode pesquisar eventos culturais nos campos de busca combinada. <br> Como usuário cadastrado, você pode incluir seus eventos na plataforma e divulgá-los gratuitamente.')) ?></p>
                    <mc-link route="search/events" class="button button--icon button--sm event__color">
                        <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </mc-link>
                </div>
            </div>

            <!-- Espaços -->
            <div v-if="global.enabledEntities.spaces" class="card card--spaces">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Espaços') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_4.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('spaces', i::__('Procure por espaços culturais incluídos na plataforma, acessando os campos de busca combinada que ajudam na precisão de sua pesquisa. <br> Cadastre também os espaços onde desenvolve suas atividades artísticas e culturais.')) ?></p>
                    <mc-link route="search/spaces" class="button button--icon button--sm space__color">
                        <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </mc-link>
                </div>
            </div>

            <!-- Agentes -->
            <div v-if="global.enabledEntities.agents" class="card card--agents">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Agentes') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_5.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('agents', i::__('Neste espaço, estão registrados artistas, gestores e produtores; uma rede de atores envolvidos na cena cultural da região. <br> Você pode cadastrar um ou mais agentes (grupos, coletivos, bandas, instituições, empresas, etc.).')) ?></p>
                    <mc-link route="search/agents" class="button button--icon button--sm agent__color">
                        <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </mc-link>
                </div>
            </div>

            <!-- Projetos -->
            <div v-if="global.enabledEntities.projects" class="card card--projects">
                <div class="card__left">
                    <div class="card__left--content">
                        <div class="card__left--content-title">
                            <label class="title">
                                <?= i::__('Projetos') ?>
                            </label>
                        </div>
                    </div>
                    <div class="card__left--img">
                        <img src="<?= $this->asset('img/boton_6.png', false) ?>" />
                    </div>
                </div>
                <div class="card__right">
                    <p><?= $this->text('projects', i::__('Aqui você encontra leis de fomento, mostras, convocatórias e editais criados, além de diversas iniciativas cadastradas pelos usuários da plataforma.')) ?></p>
                    <mc-link route="search/projects" class="button button--icon button--sm project__color">
                        <?= i::__('Ver todos')?>
                        <mc-icon name="access"></mc-icon>
                    </mc-link>
                </div>
            </div>

        </div>
    </div>
</div>
