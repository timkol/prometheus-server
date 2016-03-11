<?php

namespace App\Model;

use Nette;
use Nette\Mail\Message;
use App\Model\PlayerManager;
use Nette\Mail\SendmailMailer;

class MailManager extends Nette\Object {
    /** @var \App\Model\PlayerManager */
    private $playerManager;
    
    /** @var \App\Model\Logger */
    private $logger;
    
    /** @var Nette\DI\Container */
    private $context;
    
    /** @var Nette\Mail\SendmailMailer */
    private $mailer;

    public function __construct(Nette\DI\Container $context, \App\Model\PlayerManager $playerManager, \App\Model\Logger $logger, Nette\Mail\SendmailMailer $mailer)
    {
        $this->playerManager = $playerManager;
        $this->logger = $logger;
        $this->context = $context;
        $this->mailer = $mailer;
    }

    public function sendWinningMail($player_id){
        $player = $this->playerManager->getPlayerByPlayerId($player_id);
        $parameters = $this->context->parameters['mail']['win'];
        
        $latte = new \Latte\Engine;
        $params = array(
            'gender' => $player[PlayerManager::COLUMN_GENDER],
            'osloveni' => $player[PlayerManager::COLUMN_OSLOVENI],
            'race' => $player[PlayerManager::COLUMN_RACE],
            'name' => $player[PlayerManager::COLUMN_NAME]
        );
        
        $mail = new Message;
        $mail->setFrom($parameters['from'])
            ->addTo($player[PlayerManager::COLUMN_EMAIL])
            ->setHtmlBody($latte->renderToString($parameters['template'], $params));
        foreach ($parameters['attachments'] as $attachment) {
            $mail->addAttachment($attachment);
        }

        $this->mailer->send($mail);
        
        $this->logger->log($player_id, "Winning mail sent");
    }
    
    public function sendInvitation($player_id, $password){
        $player = $this->playerManager->getPlayerByPlayerId($player_id);
        $parameters = $this->context->parameters['mail']['invitation'];
        
        $latte = new \Latte\Engine;
        $params = array(
            'gender' => $player[PlayerManager::COLUMN_GENDER],
            'osloveni' => $player[PlayerManager::COLUMN_OSLOVENI],
            'login' => $player[PlayerManager::COLUMN_LOGIN],
            'password' => $password
        );
        
        $mail = new Message;
        $mail->setFrom($parameters['from'])
            ->addTo($player[PlayerManager::COLUMN_EMAIL])
            ->setHtmlBody($latte->renderToString($parameters['template'], $params));
        foreach ($parameters['attachments'] as $attachment) {
            $mail->addAttachment($attachment);
        }

        $this->mailer->send($mail);
        
        $this->logger->log($player_id, "Inviting mail sent");
    }
}