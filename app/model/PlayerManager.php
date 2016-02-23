<?php

namespace App\Model;

use Nette;

class PlayerManager extends Nette\Object {

    const
	TABLE_PLAYER_NAME = 'player',
	COLUMN_ID = 'player_id',
        COLUMN_LOGIN = 'login',
	COLUMN_PASSWORD_HASH = 'hash',
        COLUMN_TOKEN = 'token',
	COLUMN_NAME = 'name',
        COLUMN_LAST_ACTIVE = 'last_active',
        COLUMN_LAST_LOGIN = 'last_login';


    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
	$this->database = $database;
    }
    
    public function getPlayerByPlayerId($player_id){
        return $this->database->table(self::TABLE_PLAYER_NAME)->where(self::COLUMN_ID, $player_id)->fetch();
    }
    
    public function getAllPlayers(){
        return $this->database->table(self::TABLE_PLAYER_NAME)->fetchAll();
    }

    public function setAsActive($player_id){
        $this->getPlayerByPlayerId($player_id)->update(array(
            self::COLUMN_LAST_ACTIVE => new \DateTime()
        ));
    }
    
}