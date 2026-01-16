<?php
// 1. Define que essa página usa o layout padrão (com menu e rodapé)
$this->layout = 'element';

// 2. Adiciona uma classe no CSS caso queira customizar depois
$this->bodyProperties['class'] = 'pagina-turismo';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 40px;">
    
    <div class="text-center">
        <h1>Turismo e Cultura</h1>
        <p class="lead">Conheça os espaços culturais e turísticos da nossa cidade.</p>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <h3>Sobre o Projeto</h3>
            <p>
                Aqui você pode descrever como a Secretaria de Cultura integra o turismo local.
                Esta é uma página estática criada dentro do seu novo tema.
            </p>
            
            <a href="<?php echo $app->createUrl('site', 'search') ?>" class="btn btn-primary">
                Buscar Eventos
            </a>
        </div>
    </div>

</div>
