<?php

if ($modx->context->get('key') != 'mgr') return;

$ResourceWatcher = $modx->getService('resourcewatcher', 'ResourceWatcher', $modx->getOption('resourcewatcher.core_path', null, $modx->getOption('core_path').'components/resourcewatcher/').'model/resourcewatcher/', $scriptProperties);
if (!($ResourceWatcher instanceof ResourceWatcher)) return '';

switch ($e = $modx->event->name) {
    case 'OnDocFormSave':
        $ResourceWatcher->getParams($modx->event->params);
        break;
    case 'OnDocPublished':
        $ResourceWatcher->getParams(array("mode" => "pub"));
        break;
    default:
        break;
}
return;