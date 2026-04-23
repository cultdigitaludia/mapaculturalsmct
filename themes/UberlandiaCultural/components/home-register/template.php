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
        <label class="home-register__content--title"><?= $this->text('title', i::__('Faça seu cadastro e colabore com o <br>MAPAS CULTURAIS')) ?></label>
        <p class="home-register__content--description"><?= $this->text('description', i::__('Colabore com a plataforma livre, colaborativa e interativa de <br>mapeamento do cenário cultural e instrumento de governança digital <br>no aprimoramento da gestão pública, dos mecanismos de participação e <br>da democratização do acesso às políticas culturais <br>promovidas pela Secretaria da Cultura.')); ?>
        </p>
        <a href="<?= $app->createUrl('autenticacao', 'register') ?>" class="home-register__content--button button button--icon button--bg">
            <?= i::__('Fazer Cadastro')?>
            <mc-icon name="access"></mc-icon>
        </a>
    </div>
</div>