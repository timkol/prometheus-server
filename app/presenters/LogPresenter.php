<?php

namespace App\Presenters;

use Nette,
    App\Model\PlayerManager,
    Nette\Application\Responses\JsonResponse,
    App\Model\Logger;


class LogPresenter extends BasePresenter
{    
    /** @var \App\Model\Logger @inject*/
    public $logger;

    public function actionAdd() {
        $message = $this->request->getPost('message');
        $this->logger->log($this->identity->data[PlayerManager::COLUMN_ID], $message);
        $this->sendResponse(new JsonResponse(array(
            'status' => true
        )));
    }
    
}