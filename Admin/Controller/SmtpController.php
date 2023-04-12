<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class SmtpController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Config", "admin");
    }

    public function indexAction(): void
    {
        $entity = $this->model->getOne();
        $this->view->entity = $entity;
        $this->render('index', false);
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            if (empty($_POST['mail_password'])) {
                unset($_POST['mail_password']);
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($_POST, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Atualizou as configurações de smtp");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Configurações atualizadas.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'As Configurações não foi atualizadas.',
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