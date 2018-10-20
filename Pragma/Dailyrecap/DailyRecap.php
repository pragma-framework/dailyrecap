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

	public function getTextContent($title = null){
		$text = '';
		if(!empty($title)){
			$text = $title."\n\n";
		}
		
		$categs = $this->buildCategs();

		foreach ($categs as $categ){
            if(!empty($this->categories[$categ])){
                $text .= sprintf(_($this->categories[$categ]),count($this->messages[$categ]))."\n";
            }
            foreach ($this->messages[$categ] as $m){
                $text .= $m."\n";
            }
        }
        return strip_tags(html_entity_decode($text));
	}

	// Build categs based on messages & order it with hook(s)
	protected function buildCategs(){
		$categs = array_keys($this->messages);

		if(defined('DAILYRECAP_ORDER_CATEG') && !empty(DAILYRECAP_ORDER_CATEG)){
			if(is_array(DAILYRECAP_ORDER_CATEG)){
				foreach(DAILYRECAP_ORDER_CATEG as $hook){
					if(is_callable($hook)){
						$categs = call_user_func($hook, $categs);
					}
				}
			}elseif(is_callable(DAILYRECAP_ORDER_CATEG)){
				$categs = call_user_func(DAILYRECAP_ORDER_CATEG, $categs);
			}
		}
		return $categs;
	}
}
