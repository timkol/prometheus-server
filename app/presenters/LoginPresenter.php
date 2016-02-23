<?php

namespace App\Presenters;

use Nette,
    Nette\Application\Responses\JsonResponse,
    App\Model\PlayerManager;


class LoginPresenter extends Nette\Application\UI\Presenter
{
    /** @var \App\Model\Authentication\PasswordAuthenticator @inject*/
    public $authenticator;
    
    /** @var \App\Model\PlayerManager @inject*/
    public $playerManager;

    public function actionDefault() {
        try{
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');
            $identity = $this->authenticator->passwordAuthenticate($login, $password);
        
            $payload = array(
                'authToken' => $identity->data[PlayerManager::COLUMN_TOKEN],
                'status' => true,
                'username' => $identity->data[PlayerManager::COLUMN_NAME]
            );
        }
        catch(Nette\Security\AuthenticationException $e){
            $payload = array(
                'status' => false
            );
        }
        $this->sendResponse(new JsonResponse($payload));
    }
}