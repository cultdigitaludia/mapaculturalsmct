<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\i;

$this->import('
    mc-card
    password-strongness
');
?>

<div class="login">

    <!-- Login action -->

    <div v-if="!recoveryRequest && !recoveryMode" class="login__action">
        <div class="login__card">
            <div class="login__card__header">
                <h1> <?= $this->text('welcome', i::__('Boas-vindas!')) ?> </h1>
                <p> <?= sprintf($this->text('greeting', i::__('Entre na sua conta do %s')), $app->siteName) ?> </p>
            </div>

            <div class="login__card__content">
                <form class="login__form" @submit.prevent="doLogin();">
                    <div class="login__fields">
                        <div class="field">
                            <label for="email"> <?= i::__('E-mail ou CPF/CNPJ') ?> </label>
                            <input type="text" name="email" id="email" v-model="email" autocomplete="off" />
                        </div>

                        <div class="field password">
                            <label for="password"> <?= i::__('Senha') ?> </label>
                            <input type="password" name="password" id="password" v-model="password" autocomplete="off" />
                            <button id="multiple-login-recover" type="button" class="login__recover-link" @click="recoveryRequest = true"> <?= i::__('Esqueci minha senha') ?> </button>
                            <button type="button" class="seePassword" aria-label="<?= i::esc_attr_e('Mostrar ou ocultar senha') ?>" @click="togglePassword('password', $event)"></button>
                        </div> 
                    </div>                     

                    <VueRecaptcha v-if="configs['google-recaptcha-sitekey']" :sitekey="configs['google-recaptcha-sitekey']" @verify="verifyCaptcha" @expired="expiredCaptcha" @render="expiredCaptcha" class="g-recaptcha"></VueRecaptcha>
                    
                    <div class="login__buttons">
                        <button class=" button button--primary button--large button--md" type="submit"> <?= i::__('Entrar') ?> </button>

                        <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="divider"> 
                            <span class="divider__text"> <?= i::__('Ou entre com') ?> </span>
                        </div>

                        <div class="login__social-buttons" :class="{'login__social-buttons--multiple': multiple}">
                            <a v-if="configs.strategies.govbr?.visible" class="social-login--button button button--icon button--large button--md govbr" href="<?php echo $app->createUrl('auth', 'govbr') ?>">                                
                                <div class="img"> <img height="16" class="br-sign-in-img" src="<?php $this->asset('img/govbr-white.png'); ?>" /> </div>                                
                                <?= i::__('Entrar com Gov.br') ?>                            
                            </a>

                            <a v-if="configs.strategies.Google?.visible" class="social-login--button button button--icon button--large button--md google" href="<?php echo $app->createUrl('auth', 'google') ?>">                                
                                <div class="img"> <img height="16" src="<?php $this->asset('img/g.png'); ?>" /> </div>                                
                                <?= i::__('Entrar com Google') ?>
                            </a>

                            <a v-if="configs.strategies.decidim?.visible" class="social-login--button button button--icon button--large button--md govbr" href="<?php echo $app->createUrl('auth', 'decidim') ?>">                                
                                <span v-if="configs.strategies.decidim?.button_text">{{configs.strategies.decidim.button_text}}</span>
                                <span v-else><?= i::__('Entrar com ID Cacicadas') ?></span>                            
                            </a>
                        </div>
                    </div>

                    <div class="create">
                        <div class="create__text">
                            <h5 class="bold"><?= i::__('Ainda não tem cadastro?') ?></h5>
                            <p><?= i::__('Crie sua conta para participar da plataforma.') ?></p>
                        </div>

                        <a class="login__register-button button button--large button--md" href="<?php echo $app->createUrl('auth', 'register') ?>">
                            <?= $this->text('fazer-cadastro', i::__('Fazer cadastro')) ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recovery request -->
    <div v-if="recoveryRequest" class="login__recovery--request">
        <div class="login__card" v-if="!recoveryEmailSent">
            <div class="login__card__header">
                <h1> <?= i::__('Alteração de senha') ?> </h1>
                <p> <?= i::__('Se você esqueceu a senha, não se preocupe, todo mundo passa por isso.') ?> <br> <?= i::__('Digite seu e-mail para criar uma nova.') ?> </p>
            </div>

            <div class="login__card__content">
                <form class="grid-12" @submit.prevent="requestRecover();">
                    <div class="field col-12">
                        <label for="email"> <?= i::__('E-mail') ?> </label>
                        <input type="email" name="email" id="email" v-model="email" autocomplete="off" />
                    </div>
                    <VueRecaptcha v-if="configs['google-recaptcha-sitekey']" :sitekey="configs['google-recaptcha-sitekey']" @verify="verifyCaptcha" @expired="expiredCaptcha" @render="expiredCaptcha" class="g-recaptcha col-12"></VueRecaptcha>
                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Alterar senha') ?> </button>
                    <button type="button" @click="recoveryRequest = false" class="col-12 button button--secondarylight button--large button--md"> <?= i::__('Voltar') ?> </button>
                </form>
            </div>
        </div>

        <div class="login__card" v-if="recoveryEmailSent">
            <div class="login__card__content">
                <div class="grid-12">
                    <div class="col-12 header">
                        <label class="header__title"> <?= i::__('Alteração de senha') ?> </label>
                        <mc-icon name="circle-checked" class="header__icon"></mc-icon>
                        <label class="header__label"> <?= i::__('Enviamos as instruções de alteração de senha para seu e-mail.') ?> </label>
                    </div>

                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Não recebi o e-mail') ?> </button>
                    <button type="button" @click="recoveryEmailSent = false" class="col-12 button button--secondarylight button--large button--md"> <?= i::__('Voltar') ?> </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery action -->
    <div v-if="recoveryMode" class="login__recovery--action">
        <div class="login__card">
            <div class="login__card__header">
                <h1> <?= i::__('Redefinir senha de acesso') ?> </h1>
            </div>

            <div class="login__card__content">
                <form class="grid-12" @submit.prevent="doRecover();">
                    <div class="field col-12 password">
                        <label for="pwd"> <?= i::__('Senha'); ?> </label>
                        <input autocomplete="off" id="pwd" type="password" name="password" v-model="password" />
                        
                    </div>

                    <div class="field col-12 password">
                        <label for="pwd-confirm"> <?= i::__('Confirme sua nova senha'); ?> </label>
                        <input autocomplete="off" id="pwd-confirm" type="password" name="confirmPassword" v-model="confirmPassword" />
                    </div>

                    <div class="col-12">
                        <password-strongness :password="password"></password-strongness>
                    </div>

                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Redefinir senha') ?> </button>
                </form>
            </div>
        </div>
    </div>
</div>
