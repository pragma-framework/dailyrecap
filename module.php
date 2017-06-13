<?php
namespace Pragma\Dailyrecap;

class Module {
	public static function getDescription(){
		return array(
			"Pragma-Framework/Dailyrecap",
			array(
				"index.php dailyrecap:send\tSend daily recap",
				array("- options ?")
			),
		);
	}
}