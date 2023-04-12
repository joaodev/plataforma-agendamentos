<?php

namespace App\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;

class UseTermsController extends ActionController
{
	private mixed $model;

	public function __construct()
	{
		parent::__construct();
		$this->model = Container::getClass("UseTerms", "admin");
	}

	public function indexAction(): void
	{
		$entity = $this->model->getOne();
    	$this->view->entity = $entity;
		
		$this->render('index');
	}
}