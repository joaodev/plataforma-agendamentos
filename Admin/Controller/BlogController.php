<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class BlogController extends ActionController implements CrudInterface
{
    private mixed $model;
    private mixed $filesModel;
    private mixed $categoriesModel;
    private mixed $subcategoriesModel;
    private mixed $newsletterModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Blog", "admin");
        $this->filesModel = Container::getClass("Files", "admin");
        $this->categoriesModel = Container::getClass("Categories", "admin");
        $this->subcategoriesModel = Container::getClass("Subcategories", "admin");
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
        $categories = $this->categoriesModel->getAllActivesByType(2);
        $this->view->categories = $categories;

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
                'subtitle' => $_POST['subtitle'],
                'category_uuid' => $_POST['category_uuid'],
                'subcategory_uuid' => $_POST['subcategory_uuid'],
                'description' => $_POST['description'],
                'source_title' => $_POST['source_title'],
                'source_link' => $_POST['source_link'],
                'user_uuid' => $_SESSION['COD'],
            ];

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->create($postData);

            if ($transaction) {
                if (!empty($_FILES)) {
                    $this->filesModel->uploadFiles($_FILES, "blog", $uuid);
                }

                if ($canNotify) {
                    $this->newsletterNotification($postData);
                }

                $this->toLog("Cadastrou a Publicação $uuid");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Publicação cadastrada.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A Publicação não foi cadastrada.',
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

            $categories = $this->categoriesModel->getAllActivesByType(2);
            $this->view->categories = $categories;

            $this->render('update', false);
        }
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            $postData = [
                'title' => $_POST['title'],
                'slug' => $this->slugGenerator($_POST['title']),
                'subtitle' => $_POST['subtitle'],
                'category_uuid' => $_POST['category_uuid'],
                'subcategory_uuid' => $_POST['subcategory_uuid'],
                'description' => $_POST['description'],
                'source_title' => $_POST['source_title'],
                'source_link' => $_POST['source_link'],
                'status' => $_POST['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, $_POST['uuid'], 'uuid');

            if ($transaction) {
                if (!empty($_FILES)) {
                    $this->filesModel->uploadFiles($_FILES, "blog", $_POST['uuid']);
                }

                $this->toLog("Atualizou a Publicação {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Publicação atualizada.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A Publicação não foi atualizada.',
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
                $this->toLog("Removeu a Publicação {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Publicação removida.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'A Publicação não foi removida.',
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

    public function deleteFileAction(): bool
    {
        if (!empty($_POST)) {
            $crud = new Crud();
            $crud->setTable($this->filesModel->getTable());
            return $crud->update(['deleted' => '1'], $_POST['uuid'], 'uuid');
        } else {
            return false;
        }
    }

    public function fieldExistsAction()
    {
        if (!empty($_POST)) {
            $uuid = (!empty($_POST['uuid']) ? $_POST['uuid'] : null);
            $field = "";

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

    public function createCategoryAction(): void
    {
        $this->render('create-category', false);
    }

    public function createSubcategoryAction(): void
    {
        if (!empty($_POST['category_uuid'])) {
            $category = $this->categoriesModel->getOneActiveByUuid($_POST['category_uuid']);
            if ($category) {
                $this->view->category = $category;
                $this->render('create-subcategory', false);
            }
        }
    }

    public function subcategoriesAction(): void
    {
        if (!empty($_POST['category_uuid'])) {
            $subcateogories = $this->subcategoriesModel->getAllActives($_POST['category_uuid']);
            $this->view->subcategories = $subcateogories;
            $this->render('subcategories', false);
        }
    }

    private function newsletterNotification(array $params): void
    {
        $users = $this->newsletterModel->getAll();
        if (!empty($users)) {
            $url = baseUrl . 'post?s=' . $params['slug'];
                $message = "<p>Uma nova publicação foi adicionada!</p> 
                    <div>
                        <h1>{$params['title']}</h1> 
                        <h2>{$params['subtitle']}</h2> 
                    </div>
                    <p><a href='$url'>acesse a plataforma</a> para ler a publicação completa.</p>"; 

            foreach ($users as $user) {
                $this->sendMail([
                    'title' => 'Nova Publicação',
                    'message' => $message,
                    'name' => $user['name'],
                    'toAddress' => $user['email']
                ]);
            }
        }
    }
}
