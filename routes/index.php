<?php
use Pragma\Router\Router;
use Pragma\Dailyrecap\SendController;

$app = Router::getInstance();

$app->cli('dailyrecap:send',function(){
	SendController::sendRecap();
});
