<?php

namespace App\Model;

use Nette;

class Logger extends Nette\Object {

    const
	TABLE_LOG_NAME = 'log',
	COLUMN_ID = 'log_id',
	COLUMN_MESSAGE = 'message',
	COLUMN_PLAYER_ID = 'player_id',
        COLUMN_INSERTED = 'inserted';


    /** @var Nette\Database\Context */
    private $database;
    
    /** @var \App\Model\PlayerManager */
    private $playerManager;

    public function __construct(Nette\Database\Context $database, \App\Model\PlayerManager $playerManager)
    {
	$this->database = $database;
        $this->playerManager = $playerManager;
    }
        
    public function log($player_id, $message){
        $this->database->table(self::TABLE_LOG_NAME)->insert(array(
            self::COLUMN_PLAYER_ID => $player_id,
            self::COLUMN_MESSAGE => $message,
            self::COLUMN_INSERTED => new \DateTime()
        ));
        
        //$this->playerManager->setAsActive($player_id);
    }
    
}