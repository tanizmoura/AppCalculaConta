<?php 

namespace MF\Controller;

abstract class Action {
	protected $view;

	public function __construct(){
		$this->view = new \stdClass();
	}

	protected function render($view, $layout = 'layout') {
		$this->view->page = $view;

		if (file_exists("../App/View/".$layout.".phtml")) {
			require_once "../App/View/".$layout.".phtml";
		} else {
			$this->content();
		}
		
	}

	protected function content() {
		$classAtual =  get_class($this);
		$classAtual = str_replace('App\\Controllers\\', '', $classAtual);
		$classAtual = str_replace('Controller', '', $classAtual);

		require_once "../App/View/".$classAtual."/".$this->view->page.".phtml";
	}

}

?>