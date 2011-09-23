<?php

if ($modx->context->get('key') != 'mgr') return;

$e = $modx->event->name;
switch ($e) {
    case 'OnDocFormSave':
        $resource = $modx->event->params['resource'];
        $user = $modx->user;
        $profile = $user->getOne('Profile');
        $email = $modx->event->params['email'];

        //$message = $modx->getChunk('myEmailTemplate');
        function sendInfos($email, $subject, $message) {
            global $modx;

            $modx->getService('mail', 'mail.modPHPMailer');
            $modx->mail->set(modMail::MAIL_BODY, $message);
            $modx->mail->set(modMail::MAIL_FROM, /*$modx->getOption('emailSender')*/ 'me@melting-media.com');
            $modx->mail->set(modMail::MAIL_FROM_NAME, /*$modx->getOption('site_name')*/ 'hole');
            $modx->mail->set(modMail::MAIL_SENDER, /*$modx->getOption('site_name')*/ 'hola');
            $modx->mail->set(modMail::MAIL_SUBJECT, $subject);
            $modx->mail->address('to', $email);
            $modx->mail->address('reply-to', 'me@xexample.org');
            $modx->mail->setHTML(true);
            if (!$modx->mail->send()) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
            }
            $modx->mail->reset();
        }

        switch ($modx->event->params['mode']) {
            case 'new':
                //$modx->setPlaceholders($ph);
                $message = 'User '.$user->get('username').' just created "'.$resource->get('pagetitle').'" resource (id = '.$resource->get('id').')';
                sendInfos($email, $subject, $message);
                break;

            case 'upd':
                $modx->setPlaceholders($resource);
                $modx->setPlaceholders($user);
                $modx->setPlaceholders($profile);
                $message = $modx->getChunk($modx->event->params['tpl']);
                //$subject = $modx->event->params['subject'] ? $modx->event->params['subject'] : 'A resource has been updated';
                $subject = 'A resource has been updated';

                sendInfos($email, $subject, $message);
                break;

            default:
                break;
        }

        break;

    default:
        break;
}
return;