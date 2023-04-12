<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class ServicesController extends ActionController implements CrudInterface
{
    private mixed $model;
    private mixed $filesModel;
    private mixed $newsletterModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Services", "admin");
        $this->filesModel = Container::getClass("Files", "admin");
        $this->newsletterModel = Container::getClass("Newsletter", "admin");
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

    public function createProcessAction(): bool
    {
        if (!empty($_POST)) {
            $canNotify = false;
            if (!empty($_POST['send_newsletter'])) {
                $canNotify = true;
                unset($_POST['send_newsletter']);
            }

            $uuid = $this->model->NewUUID();
            $postData = [
                'uuid' => $uuid,
                'title' => $_POST['title'],
                'slug' => $this->slugGenerator($_POST['title']),
                'description' => $_POST['description']
            ];

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->create($postData);

            if ($transaction) {
                if (!empty($_FILES)) {
                    $this->filesModel->uploadFiles($_FILES, "services", $uuid);
                }

                if ($canNotify) {
                    $this->newsletterNotification($postData);
                }
                
                $this->toLog("Cadastrou o Serviço $uuid");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Serviço cadastrado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Serviço não foi cadastrado.',
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
        if (!empty($_POST)) {

            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;

            $files = $this->filesModel->findAllBy('file', 'parent_uuid', $_POST['uuid']);
            $this->view->files = $files;

            $this->render('read', false);
        }
    }

    public function updateAction(): void
    {
        if (!empty($_POST)) {

            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;

            $files = $this->filesModel->findAllBy('uuid, file', 'parent_uuid', $_POST['uuid']);
            $this->view->files = $files;

            $this->render('update', false);
        }
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            $postData = [
                'title' => $_POST['title'],
                'slug' => $this->slugGenerator($_POST['title']),
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, $_POST['uuid'], 'uuid');

            if ($transaction) {
                if (!empty($_FILES)) {
                    $this->filesModel->uploadFiles($_FILES, "services", $_POST['uuid']);
                }

                $this->toLog("Atualizou o Serviço {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Serviço atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Serviço não foi atualizado.',
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
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update(['deleted' => 1], $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Removeu o Serviço {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Serviço removido.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Serviço não foi removido.',
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

    public function deleteFileAction(): bool|string
    {
        if (!empty($_POST)) {
            $crud = new Crud();
            $crud->setTable($this->filesModel->getTable());
            $tt = $crud->update(['deleted' => '1'], $_POST['uuid'], 'uuid');
            var_dump($tt);
            die;
        } else {
            return false;
        }
    }

    public function fieldExistsAction()
    {
        if (!empty($_POST)) {
            $uuid = (!empty($_POST['uuid']) ? $_POST['uuid'] : null);
            $field = null;

            if (!empty($_POST['title']))
                $field = 'title';

            $exists = $this->model->fieldExists($field, $_POST[$field], 'uuid', $uuid);
            if ($exists) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
    }

    private function newsletterNotification(array $params): void
    {
        $users = $this->newsletterModel->getAll();
        if (!empty($users)) {
            $url = baseUrl . 'servico?s=' . $params['slug'];
                $message = "<p>Um novo serviço foi adicionado!</p> 
                    <div>
                        <h1>{$params['title']}</h1>  
                    </div>
                    <p><a href='$url'>acesse a plataforma</a> para ler os detalhes desde serviço.</p>"; 

            foreach ($users as $user) {
                $this->sendMail([
                    'title' => 'Novo Serviço',
                    'message' => $message,
                    'name' => $user['name'],
                    'toAddress' => $user['email']
                ]);
            }
        }
    }
}