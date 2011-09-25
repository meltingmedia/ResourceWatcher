<?php
/**
 * rw.PublishOnly
 *
 * A hook to send publish/unpublish notification only on publish.
 */
$resource = $modx->getObject('modResource', $scriptProperties['id']);

$state = $resource->get('published');

if (!$state) {
    // resource is not published, we don't want notifications
    return false;
}

return true;