<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class HomeController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Home", "admin");
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
            $postData = [
                'footer_title' => $_POST['footer_title'],
                'footer_description' => $_POST['footer_description'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($_FILES["file_services"])) {
                $image_name_sv = $_FILES["file_services"]["name"];
                if ($image_name_sv != null) {
                    $ext_img_sv = explode(".", $image_name_sv, 2);
                    $new_name_sv = md5($ext_img_sv[0]) . '.' . $ext_img_sv[1];
                    if (
                        $ext_img_sv[1] == 'jpg' || $ext_img_sv[1] == 'jpeg'
                        || $ext_img_sv[1] == 'png' || $ext_img_sv[1] == 'gif'
                    ) {
                        $tmp_name1_sv = $_FILES["file_services"]["tmp_name"];
                        $new_image_name_sv = md5($new_name_sv . time()) . '.png';
                        $dir1_sv = "../public/uploads/about/" . $new_image_name_sv;

                        if (move_uploaded_file($tmp_name1_sv, $dir1_sv)) {
                            $postData['file_services'] = $new_image_name_sv;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_services'])) {
                    $postData['file_services'] = null;
                }
            }

            if (!empty($_FILES["file_newsletter"])) {
                $image_name_nws = $_FILES["file_newsletter"]["name"];
                if ($image_name_nws != null) {
                    $ext_img_nws = explode(".", $image_name_nws, 2);
                    $new_name_nws = md5($ext_img_nws[0]) . '.' . $ext_img_nws[1];
                    if (
                        $ext_img_nws[1] == 'jpg' || $ext_img_nws[1] == 'jpeg'
                        || $ext_img_nws[1] == 'png' || $ext_img_nws[1] == 'gif'
                    ) {
                        $tmp_name1_nws = $_FILES["file_newsletter"]["tmp_name"];
                        $new_image_name_nws = md5($new_name_nws . time()) . '.png';
                        $dir1_nws = "../public/uploads/about/" . $new_image_name_nws;

                        if (move_uploaded_file($tmp_name1_nws, $dir1_nws)) {
                            $postData['file_newsletter'] = $new_image_name_nws;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_newsletter'])) {
                    $postData['file_newsletter'] = null;
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, 1, 'id');

            if ($transaction) {
                $this->toLog("Atualizou os dados da home");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Artigo atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'Os dados não foram atualizados.',
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