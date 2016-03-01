<?php

namespace App\Presenters;

use Nette,
    App\Model\PlayerManager,
    Nette\Application\Responses\JsonResponse,
    App\Model\ProgressManager,
    App\Model\ChatReceivers;


class ProgressPresenter extends BasePresenter
{    
    /** @var \App\Model\ProgressManager @inject*/
    public $progressManager;
    
    /** @var \App\Model\PlayerManager @inject*/
    public $playerManager;
    
    public function actionTrigger(){
        $table_id = (int) $this->request->getPost('table');
        $type = $this->request->getPost('type');
        $distance = $this->request->getPost('distance');
        $this->progressManager->onTableTrigger($this->identity->data[PlayerManager::COLUMN_ID], $table_id, $type, $distance);
        $this->sendResponse(new JsonResponse(array(
            'status' => true
        )));
    }
    
    public function actionAnswer(){
        $answer = $this->request->getPost('answer');
        $correct = $this->progressManager->addAnswer($this->identity->data[PlayerManager::COLUMN_ID], $answer);
        $this->sendResponse(new JsonResponse(array(
            'status' => $correct
        )));
    }
}