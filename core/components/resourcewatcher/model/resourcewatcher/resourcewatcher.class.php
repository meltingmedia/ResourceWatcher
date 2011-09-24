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
        ),$config);

        $this->modx->addPackage('resourcewatcher', $this->config['modelPath']);
        $this->modx->lexicon->load('resourcewatcher:default');
    }
    public function getParams(array $params) {
        $mode = $params['mode'];
        switch ($mode) {
            case 'upd':
                if (!$this->modx->getOption('resourcewatcher.upd_active')) break;
                $this->_update($params);
                break;

            case 'new':
                if (!$this->modx->getOption('resourcewatcher.upd_active')) break;
                $this->_create($params);
                break;

            default: break;
        }
    }
    private function _setPlaceholders($params) {
        $prefix = $this->modx->getOption('resourcewatcher.prefix') ? $this->modx->getOption('resourcewatcher.prefix') : 'rw.';
        $resource = $params['resource'];
        $user = $this->modx->user;
        $profile = $user->getOne('Profile');
        $this->modx->setPlaceholders($resource, $prefix);
        $this->modx->setPlaceholders($user, $prefix);
        $this->modx->setPlaceholders($profile, $prefix);
        $this->modx->setPlaceholder($prefix.'id', $params['id']);
    }
    private function _update($params) {
        $email = $this->modx->getOption('resourcewatcher.upd_email');
        $subject = $this->modx->getOption('resourcewatcher.upd_subject');
        $tpl = $this->modx->getOption('resourcewatcher.upd_tpl');
        $this->_setPlaceholders($params);
        $message = (!$tpl) ? $this->modx->log(modX::LOG_LEVEL_ERROR, 'Please define a valid chunk to use as notification message.') :  $this->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    private function _create($params) {
        $email = $this->modx->getOption('resourcewatcher.new_email');
        $subject = $this->modx->getOption('resourcewatcher.new_subject');
        $tpl = $this->modx->getOption('resourcewatcher.new_tpl');
        $this->_setPlaceholders($params);
        $message = (!$tpl) ? $this->modx->log(modX::LOG_LEVEL_ERROR, 'Please define a valid chunk to use as notification message.') :  $this->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    private function _sendInfos($email, $subject, $message) {
        $emails = explode(',', $email);
        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(modMail::MAIL_BODY, $message);
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('site_name'));
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