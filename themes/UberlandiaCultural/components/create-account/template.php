<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\i;

$this->import('
    entity-field
    entity-terms
    mc-card
    mc-icon
    mc-stepper
    password-strongness
');

$taxonomies = $app->getRegisteredTaxonomies("MapasCulturais\Entities\Agent");
?>

<div class="create-account"> 

    <div v-if="!created" class="create-account__title">
        <label><?= $this->text('title', i::__('Novo cadastro')) ?> </label>
        <p><?= sprintf($this->text('description', i::__('Siga os passos para criar o seu cadastro no %s.')), $app->siteName) ?> </p>
    </div>

    <!-- Creating account -->
    <mc-card v-if="!created" class="no-title">        
        <template #content> 
            <div class="create-account__timeline">
                <mc-stepper :steps="arraySteps" disable-navigation no-labels></mc-stepper>
            </div>

            <!-- First step -->
            <div v-if="step==0" class="create-account__step grid-12">
                <form class="col-12 grid-12" @submit.prevent="nextStep();">
                    <div class="field col-12" :class="{'field--error': fieldErrors.email.length}">
                        <label for="email"> <?= i::__('E-mail') ?> </label>
                        <input type="text" name="email" id="email" v-model="email" @input="clearFieldError('email')" :aria-invalid="fieldErrors.email.length > 0" aria-describedby="email-error" />
                        <p v-if="fieldErrors.email.length" id="email-error" class="create-account__field-error" role="alert">{{fieldErrors.email[0]}}</p>
                    </div>
                    <div class="field col-12">
                        <fieldset class="create-account__document-type">
                            <legend><?= i::__('Tipo de documento') ?></legend>
                            <div class="create-account__document-options">
                                <label :class="{'is-selected': documentType === 'cpf'}" for="document-type-cpf">
                                    <input id="document-type-cpf" type="radio" v-model="documentType" value="cpf" @change="changeDocumentType()" />
                                    <span>CPF</span>
                                </label>
                                <label :class="{'is-selected': documentType === 'cnpj'}" for="document-type-cnpj">
                                    <input id="document-type-cnpj" type="radio" v-model="documentType" value="cnpj" @change="changeDocumentType()" />
                                    <span>CNPJ</span>
                                </label>
                            </div>
                        </fieldset>
                        <label class="document-label" :for="documentType">
                            {{ documentType === 'cpf' ? 'CPF' : 'CNPJ' }}
                            <div class="question">
                                <VMenu class="popover">
                                    <button tabindex="-1" class="question" type="button"> <?= i::__('Por que pedimos este dado') ?> <mc-icon name="question"></mc-icon> </button>
                                    <template #popper>
                                        <?= i::__('Para previnir fraudes e por questão de segurança, sendo utilizado para verificar a identidade do <br> usuário e garantir a segurança dos processos de identificação na plataforma, <br> evitando golpes e contas falsas.') ?>
                                    </template>
                                </VMenu>
                            </div>
                        </label>
                        <input v-if="documentType === 'cpf'" type="text" name="cpf" id="cpf" v-model="cpf" v-maska data-maska="###.###.###-##" maxlength="14" @input="clearFieldError('cpf')" :class="{'create-account__input-error': fieldErrors.cpf.length}" :aria-invalid="fieldErrors.cpf.length > 0" aria-describedby="document-error" />
                        <input v-if="documentType === 'cnpj'" type="text" name="cpf" id="cnpj" v-model="cpf" maxlength="18" autocapitalize="characters" @input="formatCnpj($event); clearFieldError('cpf')" :class="{'create-account__input-error': fieldErrors.cpf.length}" :aria-invalid="fieldErrors.cpf.length > 0" aria-describedby="document-error" />
                        <p v-if="fieldErrors.cpf.length" id="document-error" class="create-account__field-error" role="alert">{{fieldErrors.cpf[0]}}</p>
                    </div>
                    <div class="field col-12 password" :class="{'field--error': fieldErrors.password.length}">
                        <label for="pwd"> <?= i::__('Senha'); ?> </label>
                        <input autocomplete="off" id="pwd" type="password" name="password" v-model="password" @input="clearFieldError('password')" :aria-invalid="fieldErrors.password.length > 0" aria-describedby="password-error" />
                        <div class="seePassword" @click="togglePassword('pwd', $event)"></div>
                        <p v-if="fieldErrors.password.length" id="password-error" class="create-account__field-error" role="alert">{{fieldErrors.password[0]}}</p>
                    </div>
                    <div class="field col-12 password" :class="{'field--error': fieldErrors.confirm_password.length}">
                        <label for="pwd-check">
                            <?= i::__('Confirme sua senha'); ?>
                        </label>
                        <input autocomplete="off" id="pwd-check" type="password" name="confirm_password" v-model="confirmPassword" @input="clearFieldError('confirm_password')" :aria-invalid="fieldErrors.confirm_password.length > 0" aria-describedby="confirm-password-error" />
                        <div class="seePassword" @click="togglePassword('pwd-check', $event)"></div>
                        <p v-if="fieldErrors.confirm_password.length" id="confirm-password-error" class="create-account__field-error" role="alert">{{fieldErrors.confirm_password[0]}}</p>
                        <p v-if="passwordRulesLoaded && !allPasswordCriteriaMet" class="create-account__password-requirements" aria-live="polite">
                            <strong><?= i::__('A senha deve conter no mínimo:'); ?></strong>
                            <span>{{pendingPasswordRequirementsText}}.</span>
                        </p>
                    </div>
                    <p v-if="fieldErrors.general.length" class="col-12 create-account__form-error" role="alert">{{fieldErrors.general[0]}}</p>
                    <VueRecaptcha v-if="configs['google-recaptcha-sitekey']" :sitekey="configs['google-recaptcha-sitekey']" @verify="verifyCaptcha" @expired="expiredCaptcha" class="g-recaptcha col-12"></VueRecaptcha>
                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Continuar') ?> </button>
                </form>
                
                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="divider col-12"></div>

                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="social-login col-12">
                    <a v-if="configs.strategies.govbr?.visible" class="social-login--button button button--icon button--large button--md govbr" href="<?php echo $app->createUrl('auth', 'govbr') ?>">                                
                        <div class="img"> <img height="16" class="br-sign-in-img" src="<?php $this->asset('img/govbr-white.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Gov.br') ?>                            
                    </a>                    
                    <a v-if="configs.strategies.Google?.visible" class="social-login--button button button--icon button--large button--md google" href="<?php echo $app->createUrl('auth', 'google') ?>">                                
                        <div class="img"> <img height="16" src="<?php $this->asset('img/g.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Google') ?>
                    </a>
                </div>
            </div>

            <!-- Terms steps -->
            <div v-show="step==index+1" v-for="(value, name, index) in terms" class="create-account__step grid-12">
                <label class="title col-12"> {{value.title}} </label>
                <div class="term col-12" v-html="value.text" :id="'term'+index" ref="terms"></div>
                <div class="divider col-12"></div>                
                <button class="col-12 button button--primary button--large button--md" :id="'acceptTerm'+index" @click="nextStep(); acceptTerm(name)"> {{value.buttonText}} </button>
                <button class="col-12 button button--text" @click="cancel()"> <?= i::__('Voltar e excluir minhas informações') ?> </button>
            </div>

            <!-- Last step -->
            <div v-if="step==totalSteps-1" class="create-account__step grid-12">
                <label class="title col-12">
                    <div class="subtitle col-12">
                        <span> <?= i::__('Falta pouco para finalizar o seu cadastro!') ?> </span>
                        <span> <?= i::__('Dê um nome e faça uma breve descrição sua.') ?> </span>
                    </div>
                </label>
                
                <div class="col-12 create-account__entity-field" :class="{'create-account__entity-field--error': agentFieldErrors.name}">
                    <entity-field :entity="agent" hide-required label=<?php i::esc_attr_e("Nome")?> prop="name" fieldDescription="<?= i::__('As pessoas irão encontrar você por esse nome.') ?>"></entity-field>
                    <p v-if="agentFieldErrors.name" class="create-account__field-error" role="alert">{{agentFieldErrors.name}}</p>
                </div>
                <div class="col-12 create-account__entity-field" :class="{'create-account__entity-field--error': agentFieldErrors.shortDescription}">
                    <entity-field :entity="agent" hide-required prop="shortDescription" label="<?php i::esc_attr_e("Mini Bio")?>"></entity-field>
                    <p v-if="agentFieldErrors.shortDescription" class="create-account__field-error" role="alert">{{agentFieldErrors.shortDescription}}</p>
                </div>
                
                <?php foreach($taxonomies as $taxonomy): ?>
                    <?php if($taxonomy->required): ?>
                        <div class="col-12 create-account__entity-terms" :class="{'create-account__entity-terms--error': agentFieldErrors.taxonomies['<?php echo $taxonomy->slug; ?>']}">
                            <entity-terms hide-required :entity="agent" :editable="true" taxonomy='<?php echo $taxonomy->slug; ?>' title="<?php i::esc_attr_e($taxonomy->description) ?>"></entity-terms>
                            <p v-if="agentFieldErrors.taxonomies['<?php echo $taxonomy->slug; ?>']" class="create-account__field-error" role="alert">{{agentFieldErrors.taxonomies['<?php echo $taxonomy->slug; ?>']}}</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <VueRecaptcha v-if="configs['google-recaptcha-sitekey']" :sitekey="configs['google-recaptcha-sitekey']" @verify="verifyCaptcha" @expired="expiredCaptcha" @render="expiredCaptcha" class="g-recaptcha col-12"></VueRecaptcha>

                <button class="col-12 button button--primary button--large button--md" @click="register()"> <?= i::__('Criar cadastro') ?></button>
            </div>
        </template>
    </mc-card>
    
    <!-- Account created -->            
    <mc-card v-if="created" class="no-title card-created">
        <template #content>
            <div class="create-account__created grid-12">
                <div class="col-12 title">
                    <svg class="title__icon create-account__success-icon" viewBox="0 0 96 96" aria-hidden="true" focusable="false">
                        <circle cx="48" cy="48" r="40" fill="currentColor"></circle>
                        <path d="M29 48.5 42 61l25-27" fill="none" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <label v-if="emailSent" class="col-12 title__label"> <?= i::__('E-mail de confirmação enviado!') ?> </label>
                    <label v-if="!emailSent" class="col-12 title__label"> <?= i::__('Seu cadastro foi criado com sucesso!') ?> </label>
                </div>

                <p v-if="emailSent" class="emailSent col-12"> <?= sprintf($this->text('email-sent', i::__('Acesse seu e-mail para confirmar a criação de seu cadastro no %s.')), $app->siteName) ?> </p>

                <a href="<?= $app->createUrl('auth') ?>" class="col-12 button button--large button--primary"> <?php i::_e('Acessar meu cadastro') ?> </a>
            </div>
        </template>
    </mc-card>
</div>
