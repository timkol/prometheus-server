<?php

namespace App\Model;

use Nette;

class ChatManager extends Nette\Object {

    const
	TABLE_CHAT_NAME = 'chat',
	//COLUMN_CHAT_ID = 'chat_id',
	COLUMN_MESSAGE = 'message',
	COLUMN_SENDER = 'sender_id',
        COLUMN_INSERTED = 'inserted',
            
        TABLE_RECEIVERS_NAME = 'receiver',
        COLUMN_CHAT_ID = 'chat_id',
        COLUMN_RECEIVER_ID = 'player_id';

    /** @var Nette\Database\Context */
    private $database;

    /** @var \App\Model\PlayerManager */
    private $playerManager;
    
    /** @var \App\Model\Logger */
    private $logger;

    public function __construct(Nette\Database\Context $database, \App\Model\PlayerManager $playerManager, \App\Model\Logger $logger)
    {
	$this->database = $database;
        $this->playerManager = $playerManager;
        $this->logger = $logger;
    }
        
    public function getNewMessages($player_id, $timestamp){
        //$this->playerManager->setAsActive($player_id);
        return $this->database->table(self::TABLE_CHAT_NAME)->where(self::COLUMN_INSERTED.' > ?', $timestamp)
                ->where(":".self::TABLE_RECEIVERS_NAME.'.'.self::COLUMN_RECEIVER_ID, $player_id)->fetchAll();
    }
    
    public function addMessage($sender_id, $message, $receivers){
        switch ($receivers){
            case ChatReceivers::ALL:
                $receivers = array();
                $players = $this->playerManager->getAllPlayers();
                foreach ($players as $player){
                    $receivers[] = $player[PlayerManager::COLUMN_ID];
                }
                break;
            case ChatReceivers::EXCEPT_SENDER:
                $receivers = array();
                $players = $this->playerManager->getAllPlayers();
                foreach ($players as $player){
                    $player_id = $player[PlayerManager::COLUMN_ID];
                    if($player_id != $sender_id){
                        $receivers[] = $player_id;
                    }
                }
                break;
        }
                
        if(is_array($receivers)){
            $row = $this->database->table(self::TABLE_CHAT_NAME)->insert(array(
                self::COLUMN_SENDER => $sender_id,
                self::COLUMN_MESSAGE => $message,
                self::COLUMN_INSERTED => new \DateTime()
            ));
            $chat_id = $row[self::COLUMN_CHAT_ID];
            
            foreach ($receivers as $receiver){
                $this->database->table(self::TABLE_RECEIVERS_NAME)->insert(array(
                    self::COLUMN_CHAT_ID => $chat_id,
                    self::COLUMN_RECEIVER_ID => $receiver
                ));
            }
            $this->logger->log($sender_id, "User successfully inserted message.");
        }
        else{
            throw new \Nette\UnexpectedValueException;
        }
    }
    
}

final class ChatReceivers{
    const ALL = 'all',
          EXCEPT_SENDER = 'except_sender';
}