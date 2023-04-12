<?php

namespace App\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;

class ServicesController extends ActionController
{
    private mixed $model;
    private mixed $filesModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Services", "admin");
        $this->filesModel = Container::getClass("Files", "admin");

        if ($this->totalActiveServices() < 1) {
            self::redirect('');
        }
    }

    public function indexAction(): void
    {
        $itemsLimit = 6;
        $page = (!empty($_GET['page']) ? $_GET['page'] : 1);
        $start = $page - 1;
        $start = $start * $itemsLimit;

        $data = $this->model->getAllActives($start, $itemsLimit);
        $totalRecords = count($this->model->getAllActives());
        $pagesTotal = $totalRecords / $itemsLimit;

        $this->view->pagesTotal = $pagesTotal;
        $this->view->page = $page;
        $this->view->data = $data;

        $this->render('index');
    }

    public function readAction(): void
    {
        if (!empty($_GET['s'])) {
            $entity = $this->model->getOneBySlug($_GET['s']);
            $this->view->entity = $entity;

            if (!empty($entity)) {
                $dataRand = $this->model->getAllRand(4, $_GET['s']);
                $this->view->dataRand = $dataRand;

                $files = $this->filesModel->findAllBy('file', 'parent_uuid', $entity['uuid']);
                $this->view->files = $files;

                $this->render('read');
            } else {
                self::redirect('servicos');
            }
        }
    }
}