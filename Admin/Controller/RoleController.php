<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class RoleController extends ActionController implements CrudInterface
{
    private mixed $model;
    private mixed $modulesModel;
    private mixed $privilegesModel;
    private mixed $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Role", "admin");
        $this->modulesModel = Container::getClass("Module", "admin");
        $this->privilegesModel = Container::getClass("Privilege", "admin");
        $this->userModel = Container::getClass("User", "admin");
    }

    public function indexAction(): void
    {
        $data = $this->model->getAll();
        $this->view->data = $data;
        $this->render('index', false);
    }

    public function createAction(): void
    {
        $this->render('create', false);
    }

    private function savePrivilege($role, $resource, $module): void
    {
        $data = [
            'uuid' => $this->model->NewUUID(),
            'role_uuid' => $role,
            'resource_uuid' => $resource,
            'module_uuid' => $module
        ];

        $crud = new Crud();
        $crud->setTable($this->privilegesModel->getTable());
        $crud->create($data);
    }

    public function createProcessAction(): bool
    {
        if (!empty($_POST)) {
            $_POST['uuid'] = $this->model->NewUUID();
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->create($_POST);

            if ($transaction) {
                $modules = $this->modulesModel->getAll();
                foreach ($modules as $module) {
                    if ($module['view_uuid'] != 0) {
                        $this->savePrivilege($_POST['uuid'], $module['view_uuid'], $module['uuid']);
                    }

                    if ($module['create_uuid'] != 0) {
                        $this->savePrivilege($_POST['uuid'], $module['create_uuid'], $module['uuid']);
                    }

                    if ($module['update_uuid'] != 0) {
                        $this->savePrivilege($_POST['uuid'], $module['update_uuid'], $module['uuid']);
                    }

                    if ($module['delete_uuid'] != 0) {
                        $this->savePrivilege($_POST['uuid'], $module['delete_uuid'], $module['uuid']);
                    }
                }

                $this->toLog("Cadastrou o perfil de acesso: {$_POST['name']} #{$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Perfil cadastrado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Perfil não foi cadastrado.',
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

    public function readAction(): void
    {
        if (!empty($_POST['uuid'])) {
            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;
            $this->render('read', false);
        }
    }

    public function updateAction(): void
    {
        if (!empty($_POST)) {
            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;

            $this->render('update', false);
        }
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            $_POST['updated_at'] = date('Y-m-d H:i:s');
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($_POST, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Atualizou o perfil de acesso: {$_POST['name']} #{$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Perfil atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Perfil não foi atualizado.',
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

    public function deleteAction(): bool
    {
        if (!empty($_POST)) {
            $checkDeletePermission = $this->userModel->checkDeletePermission($_POST['uuid']);
            if ($checkDeletePermission) {
                $updateData = [
                    'updated_at' => date('Y-m-d H:i:s'),
                    'deleted' => '1'
                ];

                $crud = new Crud();
                $crud->setTable($this->model->getTable());
                $transaction = $crud->update($updateData, $_POST['uuid'], 'uuid');

                if ($transaction) {
                    $this->toLog("Removeu o perfil de acesso: #{$_POST['uuid']}");
                    $data = [
                        'title' => 'Sucesso!',
                        'msg' => 'Perfil removido.',
                        'type' => 'success',
                        'pos' => 'top-right'
                    ];
                } else {
                    $data = [
                        'title' => 'Erro!',
                        'msg' => 'O Perfil não foi removido.',
                        'type' => 'error',
                        'pos' => 'top-center'
                    ];
                }
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'Há usuários com este nível vinculado, desvincule para excluir.',
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

    public function fieldExistsAction()
    {
        if (!empty($_POST)) {
            $uuid = (!empty($_POST['uuid']) ? $_POST['uuid'] : null);
            $field = "";

            if (!empty($_POST['name']))
                $field = 'name';

            $exists = $this->model->fieldExists($field, $_POST[$field], 'uuid', $uuid);
            if ($exists) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
    }
}