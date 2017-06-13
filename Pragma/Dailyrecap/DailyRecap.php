<?php
namespace Pragma\Dailyrecap;

use Pragma\View\View;

class DailyRecap {
	protected $view;
	protected $messages = array();
	protected $categories = array();

	protected $defaultTemplate = __DIR__.'/default.tpl.php';

	public function __construct() {
		$this->view = new View();
	}

	public function setTemplate($path) {
		$this->View->setLayout($path);
		return $this;
	}

	public function setValue($key, $value) {
		$this->View->assign($key, $value);
		return $this;
	}

	public function addMessage($html, $category = 0, $subject = null) {
		$this->Messages[$category][] = $html;
		if(!empty($subject)){
			$this->setCategoryLabel($category, $subject);
		}
		return $this;
	}

	public function clearMessages() {
		$this->Messages = $this->Categories = array();
		return $this;
	}

	public function setCategoryLabel($category, $subject) {
		$this->Categories[$category] = $subject;
		return $this;
	}

	public function render() {
		$this->View->assign('dr_messages', $this->Messages);
		$this->View->assign('dr_categories', $this->Categories);
		return $this->View->compile();
	}
}