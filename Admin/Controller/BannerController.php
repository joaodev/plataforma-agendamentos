<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Admin\Interfaces\CrudInterface;

class BannerController extends ActionController implements CrudInterface
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Banner", "admin");
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
                'title' => $_POST['title'],
                'caption' => $_POST['caption'],
                'link_1' => $_POST['link_1'],
                'title_link_1' => $_POST['title_link_1'],
                'title_link_2' => $_POST['title_link_2'],
                'link_2' => $_POST['link_2'],
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
                        $dir1 = "../public/uploads/banner/" . $new_image_name;

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
                $this->toLog("Cadastrou o Banner $uuid");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Banner cadastrado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Banner não foi cadastrado.',
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
                'title' => $_POST['title'],
                'caption' => $_POST['caption'],
                'link_1' => $_POST['link_1'],
                'title_link_1' => $_POST['title_link_1'],
                'title_link_2' => $_POST['title_link_2'],
                'link_2' => $_POST['link_2'],
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
                        $dir1 = "../public/uploads/banner/" . $new_image_name;

                        if (move_uploaded_file($tmp_name1, $dir1)) {
                            $postData['file'] = $new_image_name;
                        }
                    }
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, $_POST['uuid'], 'uuid');

            if ($transaction) {
                $this->toLog("Atualizou o Banner {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Banner atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Banner não foi atualizado.',
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
                $this->toLog("Removeu o Banner {$_POST['uuid']}");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Banner removido.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'O Banner não foi removido.',
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
}