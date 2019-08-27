<?php
	namespace App\Controllers;

	class UserController extends BaseController{
		public function show($request, $response){
			return $this->view->render($response, 'index.phtml');
		}
	}
