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
 * The base class for ResourceWatcher.
 *
 * @package resourcewatcher
 */
class ResourceWatcher {
    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('resourcewatcher.core_path', $config, $this->modx->getOption('core_path').'components/resourcewatcher/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
        ), $config);

        $this->modx->addPackage('resourcewatcher', $this->config['modelPath']);
        $this->modx->lexicon->load('resourcewatcher:default');
    }
    /**
     * Initialize the execution if set in the system settings
     *
     * @param array $params
     * @return
     */
    public function init(array $params = array()) {
        $mode = $params['mode'];
        if (!$mode) return;
        //if ($this->modx->getOption('resourcewatcher.pub_active') && $mode == 'upd') {
        if ($this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.pub_active'))->get('value') && $mode == 'upd') {
            if ($this->_checkState($params)) $this->pubState($params);
            //if ($this->_checkState($params) && $this->modx->getOption('resourcewatcher.'.$mode.'_active')) return;
            if ($this->_checkState($params) && $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_active'))->get('value')) return;
        }
        //if (!$this->modx->getOption('resourcewatcher.'.$mode.'_active')) return;
        if (!$this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_active'))->get('value')) return;
        $this->_run($params, $mode);
    }
    /**
     * Set the resource status (0 = unpublished, 1 = published)
     *
     * @param array $params
     * @return void
     */
    public function setState($params) {
        //if (!$this->modx->getOption('resourcewatcher.pub_active') || $params['mode'] == 'new') return;
        if (!$this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.pub_active'))->get('value') || $params['mode'] == 'new') return;
        //if (!$this->_runHooks('pub', $params)) return;
        $_SESSION['rw.state'] = '';
        $resource = $this->modx->getObject('modResource', $params['resource']->get('id'));
        $_SESSION['rw.state'] = $resource->get('published');
    }
    /**
     * Check if the resource state is changed
     *
     * @param array $params
     * @return bool
     */
    private function _checkState($params) {
        //if ($this->modx->getOption('resourcewatcher.pub_active')) {
        if ($this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.pub_active'))->get('value')) {
            if (!$this->_runHooks('pub', $params)) return;
            $actual = $_SESSION['rw.state'];
            $future = $params['resource']->get('published');
            if ($actual == $future) {
                // No change
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * Sends the status change notification email
     *
     * @param array $params
     * @return void
     */
    public function pubState(array $params = array()) {
        //if (!$this->modx->getOption('resourcewatcher.pub_active')) return;
        if (!$this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.pub_active'))->get('value')) return;
        $this->_run($params, 'pub');
    }
    /**
     * Execution
     *
     * @param array $params
     * @param $mode
     * @return
     */
    private function _run(array $params = array(), $mode) {
        if (!$this->_runHooks($mode, $params)) return;
        //$email = $this->modx->getOption('resourcewatcher.'.$mode.'_email');
        $email = $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_email'))->get('value');
        //$subject = $this->modx->getOption('resourcewatcher.'.$mode.'_subject');
        $subject = $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_subject'))->get('value');
        //$tpl = $this->modx->getOption('resourcewatcher.'.$mode.'_tpl');
        $tpl = $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_tpl'))->get('value');
        $this->_setPlaceholders($params);
        $message = (!$tpl) ? $this->modx->log(modX::LOG_LEVEL_ERROR, 'Please define a valid chunk to use as notification message.') :  $this->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    /**
     * Execute the hooks (if any) and return the results
     *
     * @param string $mode
     * @param array $params
     * @return bool
     */
    private function _runHooks($mode, $params) {
        //$hooks = $this->modx->getOption('resourcewatcher.'.$mode.'_hooks');
        $hooks = $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.'.$mode.'_hooks'))->get('value');
        // Let's use hooks if any
        if ($hooks) {
            // We found some, let's run them
            if ($this->_hook($hooks, $params) != true) {
                // A hook returned false, stop everything
                return false;
            }
        }
        return true;
    }
    /**
     * Manage hooks
     *
     * @param string $hooks
     * @param array $params
     * @return bool
     */
    private function _hook($hooks, array $params = array()) {
        $hooks = explode(',', $hooks);
        foreach ($hooks as $hook) {
            $hook = trim($hook);
            $isValid = $this->modx->getObject('modSnippet', array('name' => $hook));
            if (!$isValid) return false;
            if ($this->modx->runSnippet($hook, $params) != true) return false;
        }
        return true;
    }
    /**
     * Sets some basic placeholders
     *
     * @param array $params
     * @return void
     */
    private function _setPlaceholders(array $params = array()) {
        //$prefix = $this->modx->getOption('resourcewatcher.prefix');
        $prefix = $this->modx->getObject('modSystemSetting', array('key' => 'resourcewatcher.prefix'))->get('value');
        $resource = $params['resource'];
        $user = $this->modx->user;
        $profile = $user->getOne('Profile');
        $id = array('id' => $params['id']);

        $phs = array_merge($resource->toArray(), $user->toArray(), $profile->toArray(), $id);
        $this->modx->setPlaceholders($phs, $prefix);
    }
    /**
     * Sends the mails
     *
     * @param $email
     * @param $subject
     * @param $message
     * @return void
     */
    private function _sendInfos($email, $subject, $message) {
        $emails = explode(',', $email);
        //$this->modx->switchContext('web');
        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(modMail::MAIL_BODY, $message);
        /*$this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('site_name'));*/
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getObject('modSystemSetting', array('key' => 'emailsender'))->get('value'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getObject('modSystemSetting', array('key' => 'site_name'))->get('value'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getObject('modSystemSetting', array('key' => 'site_name'))->get('value'));
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        foreach ($emails as $mail) {
            // @TODO: do some mail address validation
            $mail = trim($mail);
            $this->modx->mail->address('to', $mail);
        }
        //$modx->mail->address('reply-to', 'me@xexample.org');
        $this->modx->mail->setHTML(true);
        if (!$this->modx->mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
        }
        $this->modx->mail->reset();
    }
    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name, array $properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk', array('name' => $name), true);
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name, $this->config['chunkSuffix']);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }
    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl by default.
     * @param string $suffix The suffix to add to the chunk filename.
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name, $suffix = '.chunk.tpl') {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).$suffix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name', $name);
            $chunk->setContent($o);
        }
        return $chunk;
    }
}