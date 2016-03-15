<?php

namespace App\Presenters;

use Nette,
    App\Model\PlayerManager,
    Nette\Application\Responses\JsonResponse,
    App\Model\ChatManager,
    App\Model\ChatReceivers;


class ChatPresenter extends BasePresenter
{    
    /** @var \App\Model\ChatManager @inject*/
    public $chatManager;
    
    /** @var \App\Model\PlayerManager @inject*/
    public $playerManager;

    public function actionPoll() {        
        $lastAsked = (int) $this->request->getPost('lastAsked');
        $pollInterval = $this->context->parameters['chat']['pollInterval'];
        $onlyNotifications = ($this->request->getPost('onlyNotifications') == 'True')?true:false;
        $now = time();
        
        //dirty hack
        if($lastAsked == 0) $lastAsked++;
        
        $notifications = array();
        //if($lastAsked != 0 && $lastAsked != NULL) {
        if($lastAsked != NULL) {
                if($onlyNotifications){
                    $messages = $this->chatManager->getAllMessages();
                }
                else{
                    $messages = $this->chatManager->getNewMessages($this->identity->data[PlayerManager::COLUMN_ID], $lastAsked);
                }
                foreach ($messages as $message){
                    $author = $this->playerManager->getPlayerByPlayerId($message[ChatManager::COLUMN_SENDER]);
                    $notification = array(
                        'text' => $message[ChatManager::COLUMN_MESSAGE],
                        'author' => $author[PlayerManager::COLUMN_NAME],
                        'time' => $message[ChatManager::COLUMN_INSERTED]->getTimestamp()
                    );
                    $notifications[] = $notification;
                }
            if($onlyNotifications){
                $advices = $this->chatManager->getAllNotifications($this->identity->data[PlayerManager::COLUMN_ID]);
            }
            else{
                $advices = $this->chatManager->getNewNotifications($this->identity->data[PlayerManager::COLUMN_ID], $lastAsked);
            }
            foreach ($advices as $message){
                $author = $this->playerManager->getPlayerByPlayerId($message[ChatManager::COLUMN_SENDER]);
                $notification = array(
                    'text' => $message[ChatManager::COLUMN_MESSAGE],
                    'author' => $author[PlayerManager::COLUMN_NAME],
                    'time' => $message[ChatManager::COLUMN_SENT]->getTimestamp()
                );
                $notifications[] = $notification;
            }
        }
        
        $payload = array(
            'payload' => $notifications,
            'lastAsked' => $now,
            'pollInterval' => $pollInterval,
            'stuck' => $this->playerManager->isStuck($this->identity->data[PlayerManager::COLUMN_ID]),
            'status' => true
        );
        $this->sendResponse(new JsonResponse($payload));
    }
    
    public function actionAdd(){
        //$timestamp = (int) $this->request->getPost('timestamp');
        $message = $this->request->getPost('message');
        $author = $this->request->getPost('author');
        
        if($this->identity->data[PlayerManager::COLUMN_NAME] != $author){
            $this->error("PERMISSION DENIED", \Nette\Http\Response::S401_UNAUTHORIZED);
        }
        
        $this->chatManager->addBroadcast($this->identity->data[PlayerManager::COLUMN_ID], $message);
        $this->sendResponse(new JsonResponse(array(
            'status' => true
        )));
    }
}