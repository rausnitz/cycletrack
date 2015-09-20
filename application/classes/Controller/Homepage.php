<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Homepage extends Controller {

	public function action_index()
	{
		$view = View::factory('homepage');
		$view->systems = Kohana::$config->load('bikeshares'); // list of bikeshare systems in the app

		$this->response->body($view);
	}

}
