<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class PrivilegeController extends ActionController
{
    private mixed $model;
    private mixed $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Privilege", "admin");
        $this->roleModel = Container::getClass("Role", "admin");
    }

    public function indexAction(): void
    {
        $role = $this->roleModel->getOne($_POST['uuid']);
        $this->view->role = $role;

        $data = $this->model->getAllByRoleUuid($_POST['uuid']);
        $this->view->data = $data;

        $this->render('index', false);
    }

    public function changePrivilegeAction(): bool
    {
        if (!empty($_POST)) {
            $update = [
                'status' => $_POST['status'],
            ];

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($update, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Acão aplicada ao privilegio #{$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Ação realizada.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A ação não foi realizada.',
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