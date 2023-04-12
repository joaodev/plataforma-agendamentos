<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class SubcategoriesController extends ActionController implements CrudInterface
{
    private mixed $model;
    private mixed $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Subcategories", "admin");
        $this->categoryModel = Container::getClass("Categories", "admin");
    }

    public function indexAction(): void
    {
        if (!empty($_POST['uuid'])) {
            $data = $this->model->getAll($_POST['uuid']);
            $this->view->data = $data;

            $category = $this->categoryModel->getOne($_POST['uuid']);
            $this->view->category = $category;

            $this->view->category_uuid = $_POST['uuid'];

            $this->render('index', false);
        }
    }

    public function createAction(): void
    {
        $this->render('create', false);
    }

    public function createProcessAction(): bool
    {
        if (!empty($_POST)) {
            $category = $this->categoryModel->getOne($_POST['category_uuid']);
            $uuid = $this->model->NewUUID();
            $_POST['uuid'] = $uuid;
            $_POST['slug'] = $category['slug'] . '-' . $this->slugGenerator($_POST['name']);
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->create($_POST);

            if ($transaction) {
                $this->toLog("Cadastrou a subcategoria $uuid");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Subcategoria cadastrada.',
                    'type' => 'success',
                    'pos' => 'top-right',
                    'uuid' => $uuid,
                    'name' => $_POST['name']
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A subcategoria não foi cadastrada.',
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

    public function updateAction(): void
    {
        if (!empty($_POST['uuid'])) {
            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;
            $this->render('update', false);
        }
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            $_POST['updated_at'] = date('Y-m-d H:i:s');
            $category = $this->categoryModel->getOne($_POST['category_uuid']);
            $_POST['slug'] = $category['slug'] . '-' . $this->slugGenerator($_POST['name']);
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($_POST, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Atualizou a subcategoria {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Subcategoria atualizada.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A subcategoria não foi atualizada.',
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

    public function deleteAction(): bool
    {
        if (!empty($_POST)) {
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update([
                'deleted' => '1',
                'updated_at' => date('Y-m-d H:i:s')
            ], $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Removeu a subcategoria {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Subcategoria removida.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A subcategoria não foi removida.',
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
            $categoryUuid = (!empty($_POST['category_uuid']) ? $_POST['category_uuid'] : null);
            $field = "";

            if (!empty($_POST['name']))
                $field = 'name';

            $exists = $this->model->subcategoryExists($field, $_POST[$field], 'uuid', $uuid, $categoryUuid);
            if ($exists) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
    }
}