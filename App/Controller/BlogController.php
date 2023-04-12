<?php

namespace App\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;

class BlogController extends ActionController
{
    private mixed $model;
    private mixed $filesModel;
    private mixed $categoriesModel;
    private mixed $subcategoriesModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Blog", "admin");
        $this->filesModel = Container::getClass("Files", "admin");
        $this->categoriesModel = Container::getClass("Categories", "admin");
        $this->subcategoriesModel = Container::getClass("Subcategories", "admin");

        if ($this->totalActivePublications() < 1) {
            self::redirect('');
        }
    }

    public function indexAction(): void
    {
        $categorySlug = (!empty($_GET['c']) ? $_GET['c'] : null);
        $category = null;
        $categoryUuid = '-';

        if ($categorySlug) {
            $category = $this->categoriesModel->getOneActive($categorySlug);
            $categoryUuid = $category['uuid'];
            if (!$category) {
                self::redirect('produtos');
            }
        }

        $this->view->category = $category;

        $categories = $this->getCategories();
        $this->view->categories = $categories;

        $searchKeyword = (!empty($_GET['p']) ? $_GET['p'] : '-');
        $subcategorySlug = (!empty($_GET['s']) ? ($_GET['s']) : '-');

        $subcategoryUuid = '-';
        if ($subcategorySlug != '-') {
            $subcategory = $this->subcategoriesModel->getOneActive($subcategorySlug);
            if (!$subcategory) {
                self::redirect('blog');
            }

            $subcategoryUuid = $subcategory['uuid'];
            $this->view->subcategory = $subcategory;
        }

        $itemsLimit = 6;
        $page = (!empty($_GET['page']) ? $_GET['page'] : 1);
        $start = $page - 1;
        $start = $start * $itemsLimit;

        $data = $this->model->getAllBySearch($categoryUuid, $subcategoryUuid, $searchKeyword, $start, $itemsLimit);
        $totalRecords = count($this->model->getAllBySearch($categoryUuid, $subcategoryUuid, $searchKeyword, null, null));

        $pagesTotal = $totalRecords / $itemsLimit;

        $this->view->pagesTotal = $pagesTotal;
        $this->view->page = $page;
        $this->view->data = $data;

        $this->render('index');
    }

    public function searchAction()
    {
        if (!empty($_POST)) {

            $p = $_POST['p'];
            $c = "";
            $s = "";

            $category = $this->categoriesModel->getOneActiveByUuid($_POST['c']);
            if ($category) {
                $c = $category['slug'];
            } else {
                self::redirect('blog');
            }

            if (!empty($_POST['s'])) {
                $subcategory = $this->subcategoriesModel->getOneActiveByUuid($_POST['s']);
                if ($subcategory) {
                    $s = $subcategory['slug'];
                } else {
                    self::redirect('blog');
                }
            }

            self::redirect("blog?p=$p&c=$c&s=$s");
        } else {
            self::redirect('blog');
        }
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
                self::redirect('publicacoes');
            }
        }
    }

    private function getCategories()
    {
        return $this->categoriesModel->getCategoriesWithItems('blog');
    }

    public function subcategoriesAction(): void
    {
        if (!empty($_POST['categoryUuid'])) {
            $categoryUuid = ($_POST['categoryUuid']);
            $data = $this->subcategoriesModel->getAllActives($categoryUuid);
            $this->view->data = $data;
            $this->render('subcategories', false);
        }
    }
}