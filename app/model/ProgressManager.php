<?php

namespace App\Model;

use Nette;

class ProgressManager extends Nette\Object {

    const
	TABLE_ANSWER_NAME = 'answer',
	//COLUMN_CHAT_ID = 'chat_id',
	COLUMN_PLAYER_ID = 'player_id',
        COLUMN_ANSWER = 'answer',
        COLUMN_INSERTED = 'inserted',
    
        TABLE_VISIT_NAME = 'visit',
        COLUMN_TABLE_ID = 'table_id',
        COLUMN_TYPE = 'type',
        COLUMN_DISTANCE = 'distance',
            
        TYPE_ENTER = 'enter',
        TYPE_EXIT = 'exit',
        
        DISTANCE_SEE = 'see',
        DISTANCE_READ = 'read';
    
    /** @var Nette\Database\Context */
    private $database;

    /** @var \App\Model\PlayerManager */
    private $playerManager;
    
    /** @var \App\Model\MailManager */
    private $mailMananger;
    
    /** @var \App\Model\Logger */
    private $logger;
    
    /** @var Nette\DI\Container */
    private $context;

    public function __construct(Nette\Database\Context $database, Nette\DI\Container $context, \App\Model\PlayerManager $playerManager, \App\Model\Logger $logger, \App\Model\MailManager $mailManager)
    {
	$this->database = $database;
        $this->playerManager = $playerManager;
        $this->logger = $logger;
        $this->context = $context;
        $this->mailMananger = $mailManager;
    }
    
    public function addAnswer($player_id, $answer){
        $this->database->table(self::TABLE_ANSWER_NAME)->insert(array(
            self::COLUMN_PLAYER_ID => $player_id,
            self::COLUMN_ANSWER => $answer,
            self::COLUMN_INSERTED => new \DateTime()
        ));
        
        if(in_array($answer, $this->context->parameters['answer'])){
            if(!$this->playerManager->hasAlreadyWon($player_id)){
                $this->mailMananger->sendWinningMail($player_id);
            }
            return true;
        }
        return false;
    }
    
    public function onTableTrigger($player_id, $table_id, $type, $distance){
        if(($type != self::TYPE_ENTER && $type != self::TYPE_EXIT) || 
                ($distance != self::DISTANCE_READ && $distance != self::DISTANCE_SEE)){
            throw new \Nette\NotSupportedException;
        }
        $this->database->table(self::TABLE_VISIT_NAME)->insert(array(
            self::COLUMN_PLAYER_ID => $player_id,
            self::COLUMN_TABLE_ID => $table_id,
            self::COLUMN_DISTANCE => $distance,
            self::COLUMN_TYPE => $type,
            self::COLUMN_INSERTED => new \DateTime()
        ));
    }
}