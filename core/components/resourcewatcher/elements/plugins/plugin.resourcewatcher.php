<?php
// Make sure we are in the mgr context
if ($modx->context->get('key') != 'mgr') return;
// Load ResourceWatcher class
$ResourceWatcher = $modx->getService('resourcewatcher', 'ResourceWatcher', $modx->getOption('resourcewatcher.core_path', null, $modx->getOption('core_path').'components/resourcewatcher/').'model/resourcewatcher/', $scriptProperties);
if (!($ResourceWatcher instanceof ResourceWatcher)) return '';

switch ($modx->event->name) {
    case 'OnDocFormSave':
        $ResourceWatcher->getParams($modx->event->params);
        break;

    default:
        break;
}
return;