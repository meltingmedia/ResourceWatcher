<?php
/**
 * ResourceWatcher
 *
 * Copyright 2011 by Romain Tripault // Melting Media <romain@melting-media.com>
 *
 * ResourceWatcher is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ResourceWatcher is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ResourceWatcher; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package resourcewatcher
 */
/**
 * Package in snippets
 *
 * @package resourcewatcher
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'rw.ChildOnly',
    'description' => 'Hook for ResourceWatcher',
    'snippet' => getSnippetContent($sources['snippets'].'snippet.rw.childonly.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.rw.childonly.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'rw.PublishOnly',
    'description' => 'Hook for ResourceWatcher',
    'snippet' => getSnippetContent($sources['snippets'].'snippet.rw.publishonly.php'),
),'',true,true);
/*$properties = include $sources['build'].'properties/properties.rw.childonly.php';
$snippets[0]->setProperties($properties);
unset($properties);*/

return $snippets;