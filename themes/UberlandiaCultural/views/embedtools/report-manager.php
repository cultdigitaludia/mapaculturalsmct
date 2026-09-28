<?php
/**
 * Sobrescrita do tema UberlandiaCultural baseada na view oficial do MapasCulturais
 * v7.8.14 (modules/BaseV1EmbedTools/views/embedtools/report-manager.php).
 * Diferenças: carrega a folha css/embedtools-reports.css do tema e, quando a
 * fase não possui inscrições (a view oficial não renderiza nada e o iframe fica
 * vazio), exibe uma mensagem explicativa.
 * Ao atualizar o MapasCulturais, compare este arquivo com a nova versão oficial.
 */

use MapasCulturais\i;

$app->view->enqueueStyle('app', 'reports', 'css/reports.css');
$app->view->enqueueScript('app', 'reports', 'js/ng.reports.js', ['entity.module.opportunity']);
// Padrão visual do tema aplicado ao relatório embutido (somente na tela).
$app->view->enqueueStyle('app', 'uberlandia-embedtools-reports', 'css/embedtools-reports.css', ['reports']);
$app->view->jsObject['angularAppDependencies'][] = 'ng.reports';

$module = $app->modules['Reports'];

$statusValue = $this->controller->urlData['status'] ?? 'all';

switch ($statusValue) {
    case 'all':
        $status = '> 0';
        break;
    case 'draft':
        $status = '= 0';
        break;
    case 'invalid':
        $status = '= 2';
        break;
    case 'notapproved':
        $status = '= 3';
        break;
    case 'waitlist':
        $status = '= 8';
        break;
    case 'approved':
        $status = '= 10';
        break;
    default:
        $status = '> 0';
        break;
}

$_SESSION['reportStatusRegistration'] = $status;

$app->view->jsObject['reportStatus'] = $statusValue;

$opportunity = $this->controller->requestedEntity;
$sendHook = [];
if (!$opportunity->isOpportunityPhase) {
    if ($registrationsByTime = $module->registrationsByTime($opportunity, $status)) {
        $sendHook['registrationsByTime'] = $registrationsByTime;
    }
}

if ($registrationsByStatus = $module->registrationsByStatus($opportunity)) {
    $sendHook['registrationsByStatus'] = $registrationsByStatus;
}

if ($opportunity->evaluationMethod && $opportunity->evaluationMethod->slug == 'technical') {
    if ($registrationsByEvaluation = $module->registrationsByEvaluationStatusBar($opportunity)) {
        $sendHook['registrationsByEvaluation'] = $registrationsByEvaluation;
    }
} else {
    if ($registrationsByEvaluation = $module->registrationsByEvaluation($opportunity, $statusValue)) {
        $sendHook['registrationsByEvaluation'] = $registrationsByEvaluation;
    }
}

if ($registrationsByCategory = $module->registrationsByCategory($opportunity)) {
    $sendHook['registrationsByCategory'] = $registrationsByCategory;
}

$sendHook['opportunity'] = $opportunity;

$sendHook['self'] = $module;
$sendHook['statusRegistration'] = $statusValue;
$sendHook['hidePrintButton'] = true;

if ($opportunity->canUser('@control') && $module->hasRegistrations($opportunity)) {

    $phase_name = '';
    $num = 0;
    foreach($opportunity->phases as $phase) {
        $num++;
        if ($phase->{'@entityType'} == 'opportunity' && $phase->id == $opportunity->id) {
            if ($opportunity->evaluationMethodConfiguration) {
                $n2 = $num + 1;
                $phase_name = "{$num}º {$opportunity->name} / {$n2}º {$opportunity->evaluationMethodConfiguration->name}";
            } else {
                $phase_name = "{$num}º {$opportunity->name}";
            }
            break;
        }
    }
    ?>
    <button class="btn btn-default print-reports" onclick="window.print();"><i class="fas fa-print"></i> <?php i::_e("Imprimir");?></button>
    <header class="print-only print-header">
        <h1 class="print-header__title"><?= $opportunity->name ?></h1>
        <p><?= $phase_name ?></p>
    </header>
    <?php
    $this->part('opportunity-reports', $sendHook);
} else if ($opportunity->canUser('@control')) { ?>
    <div class="alert info"><?php i::_e('Esta fase ainda não possui inscrições para gerar relatórios.'); ?></div>
<?php }
