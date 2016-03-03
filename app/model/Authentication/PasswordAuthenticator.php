<?php

namespace App\Model\Authentication;

use Nette,
    Nette\Security\AuthenticationException,
    Nette\Security\IAuthenticator,
    Nette\Security\Identity,
    Nette\Security\Passwords,
    App\Model\PlayerManager,
    App\Model\MailManager;

class PasswordAuthenticator extends Nette\Object
{

    /** @var Nette\Database\Context */
    private $database;

    /** @var \App\Model\Logger */
    private $logger;
    
    /** @var \App\Model\PlayerManager */
    private $playerManager;
    
    /** @var \App\Model\MailManager */
    private $mailManager;

    public function __construct(Nette\Database\Context $database, \App\Model\Logger $logger, \App\Model\PlayerManager $playerManager, \App\Model\MailManager $mailManager)
    {
	$this->database = $database;
        $this->logger = $logger;
        $this->playerManager = $playerManager;
        $this->mailManager = $mailManager;
    }


    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function passwordAuthenticate($username, $password)
    {
    	//list($username, $password) = $credentials;

	$row = $this->database->table(PlayerManager::TABLE_PLAYER_NAME)->where(PlayerManager::COLUMN_LOGIN, $username)->fetch();

	if (!$row) {
            throw new AuthenticationException('The username is incorrect.', IAuthenticator::IDENTITY_NOT_FOUND);
        } 
        else if (!Passwords::verify($password, $row[PlayerManager::COLUMN_PASSWORD_HASH])) {
            throw new AuthenticationException('The password is incorrect.', IAuthenticator::INVALID_CREDENTIAL);
	} 
        else if (Passwords::needsRehash($row[PlayerManager::COLUMN_PASSWORD_HASH])) {
            $row->update(array(
		PlayerManager::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
	}
        
        $row->update(array(
            PlayerManager::COLUMN_LAST_LOGIN => date('Y-m-d H:i:s'),
        ));
        
/*        $rolesRow = $this->database->table(self::TABLE_GRANT_NAME)->where(self::COLUMN_ID, $row[self::COLUMN_ID]);
        $roles = array();
        foreach ($rolesRow as $role) {
            $roles[] = $role->role->name;
        }
*/
        //$personArray = (($row->person !== null)?$row->person->toArray():array());
	//$arr = array_merge($row->toArray(), $personArray);
        $arr = $row->toArray();
	unset($arr[PlayerManager::COLUMN_PASSWORD_HASH]);
        
        $this->playerManager->setAsActive($row[PlayerManager::COLUMN_ID]);
        $this->logger->log($row[PlayerManager::COLUMN_ID], "User sucessfully logged in.");
	return new Identity($row[PlayerManager::COLUMN_ID], null, $arr);
    }
    
    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function tokenAuthenticate($token)
    {
	$row = $this->database->table(PlayerManager::TABLE_PLAYER_NAME)->where(PlayerManager::COLUMN_TOKEN, $token)->fetch();

	if (!$row) {
            throw new AuthenticationException('The token is incorrect.', IAuthenticator::IDENTITY_NOT_FOUND);
        }
        
        $arr = $row->toArray();
	unset($arr[PlayerManager::COLUMN_PASSWORD_HASH]);
        
        $this->playerManager->setAsActive($row[PlayerManager::COLUMN_ID]);
	return new Identity($row[PlayerManager::COLUMN_ID], null, $arr);
    }

    /**
     * Adds new user.
     * @param  string
     * @param  string
     * @return void
     */
    public function add($login, $password, $token, $name, $email, $gender, $osloveni, $race)
    {
	try {
            $row = $this->database->table(PlayerManager::TABLE_PLAYER_NAME)->insert(array(
		PlayerManager::COLUMN_LOGIN => $login,
		PlayerManager::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                PlayerManager::COLUMN_TOKEN => $token,
                PlayerManager::COLUMN_NAME => $name,
                PlayerManager::COLUMN_EMAIL => $email,
                PlayerManager::COLUMN_GENDER => $gender,
                PlayerManager::COLUMN_OSLOVENI => $osloveni,
                PlayerManager::COLUMN_RACE => $race
            ));
            
            $this->mailManager->sendInvitation($row[PlayerManager::COLUMN_ID], $password);
	} 
        catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
	}
    }

}



class DuplicateNameException extends \Exception
{
    protected $message = "Duplicitní jméno.";
}
