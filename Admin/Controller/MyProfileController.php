<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Db\Bcrypt;
use Core\Di\Container;
use Core\Db\Crud;

class MyProfileController extends ActionController
{
    private mixed $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("User", "admin");
    }

    public function indexAction(): void
    {
        $entity = $this->model->getOne($_SESSION['COD']);
        $this->view->entity = $entity;
        $this->render('index', false);
    }

    public function updateProcessAction(): bool
    {
        if (!empty($_POST)) {
            $exists = $this->model->fieldExists('email', $_POST['email'], 'uuid', $_SESSION['COD']);
            if ($exists) {
                $data = [
                    'title' => 'Erro!',
                    'msg' => 'Não é possível utilizar este email.',
                    'type' => 'error',
                    'pos' => 'top-center'
                ];

                echo json_encode($data);
                return true;
            }

            if (!empty($_POST['password']) && !empty($_POST['confirmation'])) {
                if (($_POST['password'] != $_POST['confirmation'])) {
                    $data = [
                        'title' => 'Erro!',
                        'msg' => 'Senhas incorretas.',
                        'type' => 'error',
                        'pos' => 'top-center'
                    ];

                    echo json_encode($data);
                    return true;
                } else {
                    unset($_POST['confirmation']);
                    $_POST['password'] = Bcrypt::hash($_POST['password']);
                }
            } else {
                unset($_POST['password']);
                unset($_POST['confirmation']);
            }


            if (!empty($_FILES) && !empty($_FILES["file"])) {
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
                        $dir1 = "../public/uploads/users/" . $new_image_name;

                        if (move_uploaded_file($tmp_name1, $dir1)) {
                            $_POST['file'] = $new_image_name;
                        }
                    }
                }
            } else {
                if (!empty($_POST['remove_image'])) {
                    $_SESSION['PHOTO'] = null;
                    $_POST['file'] = null;
                    unset($_POST['remove_image']);
                } else {
                    unset($_POST['file']);
                }
            }

            if (empty($_POST['auth2factor'])) {
                $_POST['auth2factor'] = 0;
            }

            $_POST['updated_at'] = date('Y-m-d H:i:s');
            $crud = new Crud();
            $crud->setTable($this->model->getTable());
            $transaction = $crud->update($_POST, $_SESSION['COD'], 'uuid');

            if ($transaction) {
                $_SESSION['NAME'] = $_POST['name'];
                $_SESSION['EMAIL'] = $_POST['email'];

                if (!empty($_POST['password'])) {
                    $_SESSION['PASS'] = $_POST['password'];
                }

                if (!empty($_POST['file'])) {
                    $_SESSION['PHOTO'] = $_POST['file'];
                }

                $this->toLog("Atualizou os dados através da página meu perfil");
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
}