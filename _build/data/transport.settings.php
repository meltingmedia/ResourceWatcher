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
 * Loads system settings into build
 *
 * @package resourcewatcher
 * @subpackage build
 */
$settings = array();

$settings['resourcewatcher.prefix']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.prefix']->fromArray(array(
    'key' => 'resourcewatcher.prefix',
    'value' => 'rw.',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => '',
),'',true,true);

$settings['resourcewatcher.new_active']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.new_active']->fromArray(array(
    'key' => 'resourcewatcher.new_active',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'resourcewatcher',
    'area' => 'New',
),'',true,true);

$settings['resourcewatcher.new_email']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.new_email']->fromArray(array(
    'key' => 'resourcewatcher.new_email',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'New',
),'',true,true);

$settings['resourcewatcher.new_subject']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.new_subject']->fromArray(array(
    'key' => 'resourcewatcher.new_subject',
    'value' => 'A new resource has been created',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'New',
),'',true,true);

$settings['resourcewatcher.new_tpl']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.new_tpl']->fromArray(array(
    'key' => 'resourcewatcher.new_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'New',
),'',true,true);

$settings['resourcewatcher.upd_active']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.upd_active']->fromArray(array(
    'key' => 'resourcewatcher.upd_active',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'resourcewatcher',
    'area' => 'Update',
),'',true,true);

$settings['resourcewatcher.upd_email']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.upd_email']->fromArray(array(
    'key' => 'resourcewatcher.upd_email',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'Update',
),'',true,true);

$settings['resourcewatcher.upd_subject']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.upd_subject']->fromArray(array(
    'key' => 'resourcewatcher.upd_subject',
    'value' => 'A resource has been updated',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'Update',
),'',true,true);

$settings['resourcewatcher.upd_tpl']= $modx->newObject('modSystemSetting');
$settings['resourcewatcher.upd_tpl']->fromArray(array(
    'key' => 'resourcewatcher.upd_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'resourcewatcher',
    'area' => 'Update',
),'',true,true);


return $settings;