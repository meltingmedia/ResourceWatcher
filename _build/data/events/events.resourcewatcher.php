<?php
/**
 * Adds events to ResourceWatcher plugin
 *
 * @package quip
 * @subpackage build
 */
$events = array();

$events['OnBeforeDocFormSave']= $modx->newObject('modPluginEvent');
$events['OnBeforeDocFormSave']->fromArray(array(
    'event' => 'OnDocFormSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocFormSave']= $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
    'event' => 'OnDocFormSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocPublished']= $modx->newObject('modPluginEvent');
$events['OnDocPublished']->fromArray(array(
    'event' => 'OnDocPublished',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocUnPublished']= $modx->newObject('modPluginEvent');
$events['OnDocUnPublished']->fromArray(array(
    'event' => 'OnDocUnPublished',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

return $events;