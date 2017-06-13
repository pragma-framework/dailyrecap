<?php
namespace Pragma\Dailyrecap;

use Pragma\Router\Request;

class SendController{
	public static function sendRecap(){
		$params = Request::getRequest()->parse_params(true);

		$recap = new DailyRecap();

		// Conditionné
		$recap->setTemplate(__DIR__.'/default.tpl.php');

		MailQueue::sendDailyRecap($recap);
	}
}