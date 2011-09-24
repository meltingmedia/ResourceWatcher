<?php
/**
 * ChildOnly
 *
 * A hook to send notification emails only if the resource is a
 * direct child of a given parent
 */

$resource = $scriptProperties['resource'];
$parent = $scriptProperties['parent'];

if ($resource->get('parent') != $parent) {
    // constraint not fulfilled, do nothing
    return false;
}
return true;