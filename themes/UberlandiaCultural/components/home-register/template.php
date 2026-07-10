<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;
?>
<div class="home-register">
    <div class="home-register__background">
        <img class="home-register__background--img" :src="subsite?.files?.signupBanner ? subsite?.files?.signupBanner?.url : '<?php $this->asset($app->config['module.home']['home-register']) ?>'" />
        <div class="home-register__background--mask"></div>
    </div>
    <div class="home-register__content">
        <label class="home-register__content--title"><?= $this->text('title', i::__('Faça seu cadastro e colabore com&nbsp;o MAPAS CULTURAIS')) ?></label>
        <p class="home-register__content--description"><?= $this->text('description', i::__('Contribua com a plataforma livre, colaborativa e interativa de mapeamento da cultura e do turismo em Uberlândia.<br>A iniciativa fortalece a governança digital, aprimora a gestão pública, amplia a participação social e democratiza o acesso às políticas públicas promovidas pela Secretaria Municipal de Cultura de Uberlândia.')); ?>
        </p>
        <a href="<?= $app->createUrl('autenticacao', 'register') ?>" class="home-register__content--button button button--icon button--bg">
            <?= i::__('Fazer Cadastro')?>
            <mc-icon name="access"></mc-icon>
        </a>
    </div>
</div>
