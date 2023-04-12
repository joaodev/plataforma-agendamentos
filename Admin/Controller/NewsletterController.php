<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class NewsletterController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Newsletter", "admin");
    }

    public function indexAction(): void
    {
        $data = $this->model->getAll();
        $this->view->data = $data;

        $this->render('index', false);
    }

    public function readAction(): void
    {
        if (!empty($_POST)) {
            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;

            $this->render('read', false);
        }
    }

    public function deleteAction(): bool
    {
        if (!empty($_POST)) {
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update(['deleted' => 1], $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Removeu o cadastro {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Cadastro removido.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Cadastro não foi removido.',
                    'type' => 'error',
                    'pos' => 'top-center'
                ];
            }

            echo json_encode($data);
            return true;
        } else {
            return false;
        }
    }
}