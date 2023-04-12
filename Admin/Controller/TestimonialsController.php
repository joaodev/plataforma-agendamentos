<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class TestimonialsController extends ActionController implements CrudInterface
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Testimonials", "admin");
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

            $uuid = $this->model->NewUUID();
            $postData = [
                'uuid' => $uuid,
                'name' => $_POST['name'],
                'occupation' => $_POST['occupation'],
                'description' => $_POST['description'],
            ];

            if (!empty($_FILES)) {
                $image_name = $_FILES["file"]["name"];
                if ($image_name != null) {
                    $ext_img = explode(".", $image_name, 2);
                    $new_name = md5($ext_img[0]) . '.' . $ext_img[1];
                    if (
                        $ext_img[1] == 'jpg' || $ext_img[1] == 'jpeg'
                        || $ext_img[1] == 'png' || $ext_img[1] == 'gif'
                    ) {
                        $tmp_name1 = $_FILES["file"]["tmp_name"];
                        $new_image_name = md5($new_name . time()) . '.png';
                        $dir1 = "../public/uploads/testimonials/" . $new_image_name;

                        if (move_uploaded_file($tmp_name1, $dir1)) {
                            $postData['file'] = $new_image_name;
                        }
                    }
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->create($postData);

            if ($transaction) {
                $this->toLog("Cadastrou o Depoimento $uuid");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Depoimento cadastrado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Depoimento não foi cadastrado.',
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
            $postData = [
                'name' => $_POST['name'],
                'occupation' => $_POST['occupation'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];


            if (!empty($_FILES)) {
                $image_name = $_FILES["file"]["name"];
                if ($image_name != null) {
                    $ext_img = explode(".", $image_name, 2);
                    $new_name = md5($ext_img[0]) . '.' . $ext_img[1];
                    if (
                        $ext_img[1] == 'jpg' || $ext_img[1] == 'jpeg'
                        || $ext_img[1] == 'png' || $ext_img[1] == 'gif'
                    ) {
                        $tmp_name1 = $_FILES["file"]["tmp_name"];
                        $new_image_name = md5($new_name . time()) . '.png';
                        $dir1 = "../public/uploads/testimonials/" . $new_image_name;

                        if (move_uploaded_file($tmp_name1, $dir1)) {
                            $postData['file'] = $new_image_name;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image'])) {
                    $postData['file'] = null;
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Atualizou o Depoimento {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Depoimento atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Depoimento não foi atualizado.',
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
                $this->toLog("Removeu o Depoimento {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Depoimento removido.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Depoimento não foi removido.',
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