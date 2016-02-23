<?php

namespace App\Presenters;

use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Security\Identity */
    protected $identity;
    
    /** @var \App\Model\Authentication\PasswordAuthenticator @inject*/
    public $authenticator;

    public function startup() {
        parent::startup();
        try{
            $token = $this->request->getPost('authToken');
            $this->identity = $this->authenticator->tokenAuthenticate($token);
        }
        catch(Nette\Security\AuthenticationException $e){
            $this->handleError($e);
        }
    }
    
    protected function handleError(Nette\Security\AuthenticationException $e){
        $this->error("PERMISSION DENIED", \Nette\Http\Response::S401_UNAUTHORIZED);
    }
}