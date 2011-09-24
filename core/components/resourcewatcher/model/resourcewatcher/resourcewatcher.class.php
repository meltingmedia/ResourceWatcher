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
				
            case 'pub':
                if (!$this->modx->getOption('resourcewatcher.pub_active')) break;
                $this->_publish($params);
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
        $message = (!$tpl) ? 'I am the default message!' :  $this->modx->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    private function _publish($params) {
        $email = $this->modx->getOption('resourcewatcher.pub_email');
        $subject = $this->modx->getOption('resourcewatcher.pub_subject');
        $tpl = $this->modx->getOption('resourcewatcher.pub_tpl');
        $this->_setPlaceholders($params);
        $message = (!$tpl) ? 'I am the default message!' :  $this->modx->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    private function _create($params) {
        $email = $this->modx->getOption('resourcewatcher.new_email');
        $subject = $this->modx->getOption('resourcewatcher.new_subject');
        $tpl = $this->modx->getOption('resourcewatcher.new_tpl');
        $this->_setPlaceholders($params);
        $message = (!$tpl) ? 'I am the default message!' :  $this->modx->getChunk($tpl);
        $this->_sendInfos($email, $subject, $message);
    }
    private function _sendInfos($email, $subject, $message) {
        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(modMail::MAIL_BODY, $message);
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        $this->modx->mail->address('to', $email);
        //$modx->mail->address('reply-to', 'me@xexample.org');
        $this->modx->mail->setHTML(true);
        if (!$this->modx->mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
        }
        $this->modx->mail->reset();
    }
}