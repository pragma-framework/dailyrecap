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
		$this->view->setLayout($path);
		return $this;
	}

	public function setValue($key, $value) {
		$this->view->assign($key, $value);
		return $this;
	}

	public function addMessage($html, $category = 0, $subject = null) {
		$this->messages[$category][] = $html;
		if(!empty($subject)){
			$this->setCategoryLabel($category, $subject);
		}
		return $this;
	}

	public function clearMessages() {
		$this->messages = $this->categories = array();
		return $this;
	}

	public function setCategoryLabel($category, $subject) {
		$this->categories[$category] = $subject;
		return $this;
	}

	public function render($title = null) {
		$this->view->assign('dr_messages', $this->messages);
		$this->view->assign('dr_categories', $this->categories);
		$this->view->assign('dr_title', $title);
		return $this->view->compile();
	}
}