<?php
namespace Pragma\Dailyrecap;

use Pragma\Router\Request;
use Pragma\Helpers\TaskLock;

class SendController{
	public static function sendRecap(){
		TaskLock::check_lock(realpath('.').'/locks', 'dailyrecap');
		$params = Request::getRequest()->parse_params(false);

		$recap = new DailyRecap();

		// Template
		if(!empty($params['template']) && file_exists($params['template'])){
			$recap->setTemplate($params['template']);
		}elseif(defined('PRAGMA_MAIL_TEMPLATE') && file_exists(PRAGMA_MAIL_TEMPLATE)){
			$recap->setTemplate(PRAGMA_MAIL_TEMPLATE);
		}else{
			$recap->setTemplate(__DIR__.'/default.tpl.php');
		}

		// Subject
		if(!empty($params['subject'])){
			MailQueue::sendDailyRecap($recap, $params['subject']);
		}elseif(defined('PRAGMA_MAIL_SUBJECT') && !empty(PRAGMA_MAIL_SUBJECT)){
			MailQueue::sendDailyRecap($recap, PRAGMA_MAIL_SUBJECT);
		}else{
			MailQueue::sendDailyRecap($recap);
		}
		TaskLock::flush(realpath('.').'/locks', 'dailyrecap');
	}
}