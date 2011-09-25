<?php
// Make sure we are in the mgr context
if ($modx->context->get('key') != 'mgr') return;
// Load ResourceWatcher class
$ResourceWatcher = $modx->getService('resourcewatcher', 'ResourceWatcher', $modx->getOption('resourcewatcher.core_path', null, $modx->getOption('core_path').'components/resourcewatcher/').'model/resourcewatcher/', $scriptProperties);
if (!($ResourceWatcher instanceof ResourceWatcher)) return '';

$e = $modx->event->name;

switch ($e) {
    case 'OnBeforeDocFormSave':
        $ResourceWatcher->setState($modx->event->params);
        break;

    case 'OnDocFormSave':
        $ResourceWatcher->init($modx->event->params);
        break;

    case 'OnDocPublished':
        $ResourceWatcher->pubState(1, $modx->event->params);
        break;

    case 'OnDocUnPublished':
        $ResourceWatcher->pubState(0, $modx->event->params);
        break;

    default:
        break;
}
return;