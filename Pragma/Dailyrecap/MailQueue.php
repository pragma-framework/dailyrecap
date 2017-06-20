<?php
namespace Pragma\Dailyrecap;

use Pragma\ORM\Model;

use Pragma\Dailyrecap\Mail;

class MailQueue extends Model{
	const TABLE_NAME = 'mail_queue';

	public function __construct(){
		return parent::__construct(self::getTableName());
	}

	public static function getTableName(){
		defined('DB_PREFIX') OR define('DB_PREFIX','pragma_');
		return DB_PREFIX.self::TABLE_NAME;
	}

	public static function sendDailyRecap(DailyRecap $dailyrecap, $title = 'Récapitulatif'){
		$mails = self::forge()
			->where('when', '<=', date('Y-m-d'))
			->get_objects();
		$recap = array();
		$from = null;
		foreach($mails as $m){
			$tos = json_decode($m->to,true);
			$from = $m->from;
			foreach($tos as $t){
				if(!isset($recap[$t])){
					$recap[$t] = array();
				}
				$recap[$t][] = array(
					'category' => $m->category,
					'subject' => $m->subject,
					'html' => $m->html,
				);
			}
			$m->delete();
		}

		foreach($recap as $to => $meta){
			$dailyrecap->clearMessages();

			foreach($meta as $m){
				$dailyrecap->addMessage($m['html'], $m['category'], $m['subject']);
			}

			$mail = new Mail(
				$from,
				[$to],
				$title,
				$dailyrecap->render()
			);
			$mail->sendMail();
		}
	}
}
