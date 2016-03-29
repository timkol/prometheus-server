<?php

use App\Model\PlayerManager;

$container = require __DIR__ . '/../app/bootstrap.php';
$playerManager = $container->getByType('App\Model\PlayerManager');
$mailManager = $container->getByType('App\Model\MailManager');

$losingPlayers = $playerManager->getAllNotWinningPlayers();
foreach($losingPlayers as $player){
    $player->update(array(
        PlayerManager::COLUMN_WON => 2
    ));
    $mailMananger->sendWinningMail($player[PlayerManager::COLUMN_ID]);
}
