<?php

namespace App\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;

class IndexController extends ActionController
{
    private mixed $homeModel;
    private mixed $bannerModel;
    private mixed $blogModel;
    private mixed $testimonialsModel;
    private mixed $newsletterModel;
    private mixed $servicesModel;

    public function __construct()
    {
        parent::__construct();
        $this->homeModel = Container::getClass("Home", "admin");
        $this->bannerModel = Container::getClass("Banner", "admin");
        $this->blogModel = Container::getClass("Blog", "admin");
        $this->testimonialsModel = Container::getClass("Testimonials", "admin");
        $this->newsletterModel = Container::getClass("Newsletter", "admin");
        $this->servicesModel = Container::getClass("Services", "admin");
    }

    public function indexAction(): void
    {
        $home = $this->homeModel->getOne();
        $this->view->home = $home;

        $banners = $this->bannerModel->getAllActives();
        $this->view->banners = $banners;

        $testimonials = $this->testimonialsModel->getAllActives();
        $this->view->testimonials = $testimonials;

        $publication = $this->blogModel->getAllActives(0, 1);
        $this->view->publications = $publication;

        $services = $this->servicesModel->getAllActives(0, 3);
        $this->view->services = $services;

        $this->render('index');
    }

    public function insertNewsletterAction(): void
    {
        if (!empty($_POST)) {
            $model = $this->newsletterModel->fieldExists('email', $_POST['email'], 'uuid');
            if (!$model) {
                $_POST['uuid'] = $this->newsletterModel->NewUUID();
                $crud = new Crud();
                $crud->setTable($this->newsletterModel->getTable());
                $crud->create($_POST);
            }

            self::redirect("", "cadastro-efetuado");
        } else {
            self::redirect("");
        }
    }

    public function cancelNewsletterAction(): void
    {
        if (!empty($_POST)) {
            $model = $this->newsletterModel->fieldExists('email', $_POST['email'], 'uuid');
            if (!$model) {
                $crud = new Crud();
                $crud->setTable($this->newsletterModel->getTable());
                $crud->update(['deleted' => '1'], 'email', $_POST['email']);
            }

            self::redirect("", "inscricao-removida");
        } else {
            $this->render('cancel-newsletter');
        }
    }
}
