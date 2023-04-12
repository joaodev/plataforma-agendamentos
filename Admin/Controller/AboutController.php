<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class AboutController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("About", "admin");
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
                'title_1' => $_POST['title_1'],
                'description_1' => $_POST['description_1'],
                'title_2' => $_POST['title_2'],
                'description_2' => $_POST['description_2'],
                'block_title' => $_POST['block_title'],
                'block_button_title' => $_POST['block_button_title'],
                'block_button_link' => $_POST['block_button_link'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($_FILES["file_1"])) {
                $image_name_f1 = $_FILES["file_1"]["name"];
                if ($image_name_f1 != null) {
                    $ext_img_f1 = explode(".", $image_name_f1, 2);
                    $new_name_f1 = md5($ext_img_f1[0]) . '.' . $ext_img_f1[1];
                    if (
                        $ext_img_f1[1] == 'jpg' || $ext_img_f1[1] == 'jpeg'
                        || $ext_img_f1[1] == 'png' || $ext_img_f1[1] == 'gif'
                    ) {
                        $tmp_name1_f1 = $_FILES["file_1"]["tmp_name"];
                        $dir1_f1 = "../public/uploads/about/" . $image_name_f1;
                        if (move_uploaded_file($tmp_name1_f1, $dir1_f1)) {
                            $this->setWatermark($dir1_f1);
                            $postData['file_1'] = $image_name_f1;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image'])) {
                    $postData['file_1'] = null;
                }
            }

            if (!empty($_FILES["file_2"])) {
                $image_name_f2 = $_FILES["file_2"]["name"];
                if ($image_name_f2 != null) {
                    $ext_img_f2 = explode(".", $image_name_f2, 2);
                    $new_name_f2 = md5($ext_img_f2[0]) . '.' . $ext_img_f2[1];
                    if (
                        $ext_img_f2[1] == 'jpg' || $ext_img_f2[1] == 'jpeg'
                        || $ext_img_f2[1] == 'png' || $ext_img_f2[1] == 'gif'
                    ) {
                        $tmp_name1_f2 = $_FILES["file_2"]["tmp_name"];
                        $new_image_name_f2 = md5($new_name_f2 . time()) . '.png';
                        $dir1_f2 = "../public/uploads/about/" . $image_name_f2;

                        if (move_uploaded_file($tmp_name1_f2, $dir1_f2)) {
                            $this->setWatermark($dir1_f2);
                            $postData['file_2'] = $image_name_f2;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_2'])) {
                    $postData['file_2'] = null;
                }
            }

            if (!empty($_FILES["block_banner"])) {
                $image_name_bl = $_FILES["block_banner"]["name"];
                if ($image_name_bl != null) {
                    $ext_img_bl = explode(".", $image_name_bl, 2);
                    $new_name_bl = md5($ext_img_bl[0]) . '.' . $ext_img_bl[1];
                    if (
                        $ext_img_bl[1] == 'jpg' || $ext_img_bl[1] == 'jpeg'
                        || $ext_img_bl[1] == 'png' || $ext_img_bl[1] == 'gif'
                    ) {
                        $tmp_name1_bl = $_FILES["block_banner"]["tmp_name"];
                        $new_image_name_bl = md5($new_name_bl . time()) . '.png';
                        $dir1_bl = "../public/uploads/about/" . $image_name_bl;

                        if (move_uploaded_file($tmp_name1_bl, $dir1_bl)) {
                            $postData['block_banner'] = $image_name_bl;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_banner'])) {
                    $postData['block_banner'] = null;
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, 1, 'id');

            if ($transaction) {
                $this->toLog("Atualizou os dados do Sobre Nós");
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