<?php
/**
 * Package in plugins
 *
 * @package resourcewatcher
 * @subpackage build
 */
$plugins = array();

// create the plugin object
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id', 1);
$plugins[0]->set('name', 'ResourceWatcher');
$plugins[0]->set('description', 'Send an email upon resource creation/update.');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'plugin.resourcewatcher.php'));
$plugins[0]->set('category', 0);

$events = include $sources['events'].'events.resourcewatcher.php';
if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in '.count($events).' Plugin Events for ResourceWatcher.');
    flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find plugin events for ResourceWatcher!');
}
unset($events);

return $plugins;