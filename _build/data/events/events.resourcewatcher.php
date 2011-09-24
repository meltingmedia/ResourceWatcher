<?php
/**
 * Adds events to ResourceWatcher plugin
 *
 * @package quip
 * @subpackage build
 */
$events = array();

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

return $events;