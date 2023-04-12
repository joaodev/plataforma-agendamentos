<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;

class IndexController extends ActionController
{
    private mixed $userModel;
    private mixed $publicationsModel;
    private mixed $testimonialsModel;
    private mixed $categoriesModel;
    private mixed $customersModel;
    private mixed $servicesModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = Container::getClass("User", "admin");
        $this->publicationsModel = Container::getClass("Blog", "admin");
        $this->testimonialsModel = Container::getClass("Testimonials", "admin");
        $this->categoriesModel = Container::getClass("Categories", "admin");
        $this->customersModel = Container::getClass("Customers", "admin");
        $this->servicesModel = Container::getClass("Services", "admin");
    }

    public function indexAction(): void
    {
        $total_users = $this->userModel->totalUsers();
        $this->view->total_users = $total_users;

        $total_publications = $this->publicationsModel->totalPublications();
        $this->view->total_publications = $total_publications;

        $total_testimonials = $this->testimonialsModel->totalTestimonials();
        $this->view->total_testimonials = $total_testimonials;

        $total_categories = $this->categoriesModel->totalCategories();
        $this->view->total_categories = $total_categories;

        $total_services = $this->servicesModel->totalServices();
        $this->view->total_services = $total_services;

        $total_customers = $this->customersModel->totalCustomers();
        $this->view->total_customers = $total_customers;

        $this->render('index');
    }
}