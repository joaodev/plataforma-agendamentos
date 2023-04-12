<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class InternalBannersController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("InternalBanners", "admin");
    }

    public function indexAction(): void
    {
        $entity = $this->model->getOne();
        $this->view->entity = $entity;
        $this->render('index', false);
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_FILES) || !empty($_POST)) {
            $postData = [
                'updated_at' => date('Y-m-d H:i:s')
            ];


            if (!empty($_FILES['file_about'])) {
                $image_name_about = $_FILES["file_about"]["name"];
                if ($image_name_about != null) {
                    $ext_img_about = explode(".", $image_name_about, 2);
                    $new_name_about = md5($ext_img_about[0]) . '.' . $ext_img_about[1];
                    if (
                        $ext_img_about[1] == 'jpg' || $ext_img_about[1] == 'jpeg'
                        || $ext_img_about[1] == 'png' || $ext_img_about[1] == 'gif'
                    ) {
                        $tmp_name1_about = $_FILES["file_about"]["tmp_name"];
                        $new_image_name_about = md5($new_name_about . time()) . '.png';
                        $dir1_about = "../public/uploads/banner/" . $new_image_name_about;
                        if (move_uploaded_file($tmp_name1_about, $dir1_about)) {
                            $postData['file_about'] = $new_image_name_about;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_about'])) {
                    $postData['file_about'] = null;
                }
            }

            if (!empty($_FILES['file_publications'])) {
                $image_name_publications = $_FILES["file_publications"]["name"];
                if ($image_name_publications != null) {
                    $ext_img_publications = explode(".", $image_name_publications, 2);
                    $new_name_publications = md5($ext_img_publications[0]) . '.' . $ext_img_publications[1];
                    if (
                        $ext_img_publications[1] == 'jpg' || $ext_img_publications[1] == 'jpeg'
                        || $ext_img_publications[1] == 'png' || $ext_img_publications[1] == 'gif'
                    ) {
                        $tmp_name1_publications = $_FILES["file_publications"]["tmp_name"];
                        $new_image_name_publications = md5($new_name_publications . time()) . '.png';
                        $dir1_publications = "../public/uploads/banner/" . $new_image_name_publications;
                        if (move_uploaded_file($tmp_name1_publications, $dir1_publications)) {
                            $postData['file_publications'] = $new_image_name_publications;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_publications'])) {
                    $postData['file_publications'] = null;
                }
            }

            if (!empty($_FILES['file_contact'])) {
                $image_name_contact = $_FILES["file_contact"]["name"];
                if ($image_name_contact != null) {
                    $ext_img_contact = explode(".", $image_name_contact, 2);
                    $new_name_contact = md5($ext_img_contact[0]) . '.' . $ext_img_contact[1];
                    if (
                        $ext_img_contact[1] == 'jpg' || $ext_img_contact[1] == 'jpeg'
                        || $ext_img_contact[1] == 'png' || $ext_img_contact[1] == 'gif'
                    ) {
                        $tmp_name1_contact = $_FILES["file_contact"]["tmp_name"];
                        $new_image_name_contact = md5($new_name_contact . time()) . '.png';
                        $dir1_contact = "../public/uploads/banner/" . $new_image_name_contact;
                        if (move_uploaded_file($tmp_name1_contact, $dir1_contact)) {
                            $postData['file_contact'] = $new_image_name_contact;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_contact'])) {
                    $postData['file_contact'] = null;
                }
            }

            if (!empty($_FILES['file_services'])) {
                $image_name_services = $_FILES["file_services"]["name"];
                if ($image_name_services != null) {
                    $ext_img_services = explode(".", $image_name_services, 2);
                    $new_name_services = md5($ext_img_services[0]) . '.' . $ext_img_services[1];
                    if (
                        $ext_img_services[1] == 'jpg' || $ext_img_services[1] == 'jpeg'
                        || $ext_img_services[1] == 'png' || $ext_img_services[1] == 'gif'
                    ) {
                        $tmp_name1_services = $_FILES["file_services"]["tmp_name"];
                        $new_image_name_services = md5($new_name_services . time()) . '.png';
                        $dir1_services = "../public/uploads/banner/" . $new_image_name_services;
                        if (move_uploaded_file($tmp_name1_services, $dir1_services)) {
                            $postData['file_services'] = $new_image_name_services;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_services'])) {
                    $postData['file_services'] = null;
                }
            }

            if (!empty($_FILES['file_properties'])) {
                $image_name_properties = $_FILES["file_properties"]["name"];
                if ($image_name_properties != null) {
                    $ext_img_properties = explode(".", $image_name_properties, 2);
                    $new_name_properties = md5($ext_img_properties[0]) . '.' . $ext_img_properties[1];
                    if (
                        $ext_img_properties[1] == 'jpg' || $ext_img_properties[1] == 'jpeg'
                        || $ext_img_properties[1] == 'png' || $ext_img_properties[1] == 'gif'
                    ) {
                        $tmp_name1_properties = $_FILES["file_properties"]["tmp_name"];
                        $new_image_name_properties = md5($new_name_properties . time()) . '.png';
                        $dir1_properties = "../public/uploads/banner/" . $new_image_name_properties;
                        if (move_uploaded_file($tmp_name1_properties, $dir1_properties)) {
                            $postData['file_properties'] = $new_image_name_properties;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_properties'])) {
                    $postData['file_properties'] = null;
                }
            }

            if (!empty($_FILES['file_politics'])) {
                $image_name_politics  = $_FILES["file_politics"]["name"];
                if ($image_name_politics != null) {
                    $ext_img_politics = explode(".", $image_name_politics, 2);
                    $new_name_politics  = md5($ext_img_politics[0]) . '.' . $ext_img_politics[1];
                    if (
                        $ext_img_politics[1] == 'jpg' || $ext_img_politics[1] == 'jpeg'
                        || $ext_img_politics[1] == 'png' || $ext_img_politics[1] == 'gif'
                    ) {
                        $tmp_name1_politics  =  $_FILES["file_politics"]["tmp_name"];
                        $new_image_name_politics = md5($new_name_politics . time()) . '.png';
                        $dir1_politics = "../public/uploads/banner/" . $new_image_name_politics;
                        if (move_uploaded_file($tmp_name1_politics, $dir1_politics)) {
                            $postData['file_politics'] = $new_image_name_politics;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_politics'])) {
                    $postData['file_politics'] = null;
                }
            }

            if (!empty($_FILES['file_use_terms'])) {
                $image_name_use_terms  = $_FILES["file_use_terms"]["name"];
                if ($image_name_use_terms != null) {
                    $ext_img_use_terms = explode(".", $image_name_use_terms, 2);
                    $new_name_use_terms  = md5($ext_img_use_terms[0]) . '.' . $ext_img_use_terms[1];
                    if (
                        $ext_img_use_terms[1] == 'jpg' || $ext_img_use_terms[1] == 'jpeg'
                        || $ext_img_use_terms[1] == 'png' || $ext_img_use_terms[1] == 'gif'
                    ) {
                        $tmp_name1_use_terms  =  $_FILES["file_use_terms"]["tmp_name"];
                        $new_image_name_use_terms = md5($new_name_use_terms . time()) . '.png';
                        $dir1_use_terms = "../public/uploads/banner/" . $new_image_name_use_terms;
                        if (move_uploaded_file($tmp_name1_use_terms, $dir1_use_terms)) {
                            $postData['file_use_terms'] = $new_image_name_use_terms;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_use_terms'])) {
                    $postData['file_use_terms'] = null;
                }
            }

            if (!empty($_FILES['file_customers'])) {
                $image_name_customers  = $_FILES["file_customers"]["name"];
                if ($image_name_customers != null) {
                    $ext_img_customers = explode(".", $image_name_customers, 2);
                    $new_name_customers  = md5($ext_img_customers[0]) . '.' . $ext_img_customers[1];
                    if (
                        $ext_img_customers[1] == 'jpg' || $ext_img_customers[1] == 'jpeg'
                        || $ext_img_customers[1] == 'png' || $ext_img_customers[1] == 'gif'
                    ) {
                        $tmp_name1_customers  =  $_FILES["file_customers"]["tmp_name"];
                        $new_image_name_customers = md5($new_name_customers . time()) . '.png';
                        $dir1_customers = "../public/uploads/banner/" . $new_image_name_customers;
                        if (move_uploaded_file($tmp_name1_customers, $dir1_customers)) {
                            $postData['file_customers'] = $new_image_name_customers;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image_customers'])) {
                    $postData['file_customers'] = null;
                }
            }

            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($postData, 1, 'id');

            if ($transaction) {
                $this->toLog("Atualizou os banners internos");
                $data = [
                    'title' => 'Sucesso!',
                    'msg' => 'Artigo atualizado.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            } else {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'Os banners internos não foram atualizados.',
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
