<?php
namespace Pragma\Dailyrecap;

class Module {
	public static function getDescription(){
		return array(
			"Pragma-Framework/Dailyrecap",
			array(
				"index.php dailyrecap:send\tSend daily recap",
				array(
					"--template=[file]\tDefined which mail template is to be used for daily recap or use PRAGMA_MAIL_TEMPLATE",
					"--subject=[subject]\tDefined which mail subject is to be used for daily recap or use PRAGMA_MAIL_SUBJECT",
				),
			),
		);
	}
}