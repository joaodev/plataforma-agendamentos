<?php

namespace Admin\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Core\Db\Bcrypt;
use XLSXReader;

class ImporterController extends ActionController
{
    private mixed $model;
    private mixed $itemsModel;
    private mixed $categoriesModel;
    private mixed $subcategoriesModel;
    private mixed $customersModel;
    private mixed $usersModel;
    private mixed $roleModel;
    private mixed $modulesModel;
    private mixed $privilegesModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = Container::getClass("Imports", "admin");
        $this->itemsModel = Container::getClass("ImportItems", "admin");
        $this->categoriesModel = Container::getClass("Categories", "admin");
        $this->subcategoriesModel = Container::getClass("Subcategories", "admin");
        $this->customersModel = Container::getClass("Customers", "admin");
        $this->usersModel = Container::getClass("User", "admin");
        $this->roleModel = Container::getClass("Role", "admin");
        $this->modulesModel = Container::getClass("Module", "admin");
        $this->privilegesModel = Container::getClass("Privilege", "admin");
    }

    public function indexAction(): void
    {
        $data = $this->model->getAll();
        $this->view->data = $data;
        $this->render('index', false);
    }

    public function readAction(): void
    {
        if (!empty($_POST['uuid'])) {
            $entity = $this->model->getOne($_POST['uuid']);
            $this->view->entity = $entity;
            $uuid = $_POST['uuid'];

            switch ($entity['module']) {
                case 'categorias':
                    $items = $this->itemsModel->getAllCategories($uuid);
                    break;
                case 'clientes':
                    $items = $this->itemsModel->getAllCustomers($uuid);
                    break;
                case 'colaboradores':
                    $items = $this->itemsModel->getAllUsers($uuid);
                    break;
                default:
                    $items = [];
                    break;
            }

            $this->view->items = $items;
            $this->render('read', false);
        }
    }

    public function createAction(): void
    {
        $this->render('create', false);
    }

    public function createProcessAction(): bool
    {
        if (!empty($_POST) && !empty($_FILES)) {
            $xlsx = new XLSXReader($_FILES['file']['tmp_name']);
            $sheetNames = $xlsx->getSheetNames();
            $xlsx_field_value = [];

            foreach ($sheetNames as $sheetName) {
                $sheet = $xlsx->getSheet($sheetName);
                $xlsx_data = $sheet->getData();
           
                foreach ($xlsx_data as $row_xlsx) {
                    $xlsx_field_value[] = $row_xlsx;
                }
            }

            $count = 0;
            $total = 0;
            $items = [];
            
            foreach ($xlsx_field_value as $dataToImport) {
                $count++;
                if ($count > 1) {
                    if ($_POST['module'] == 'categorias') {
                        $newRow = $this->setCategory($dataToImport[0]);
                        $subcategories = explode(",", $dataToImport[1]);
                        $this->setSubcategories($subcategories, $newRow['uuid']);
                    }

                    if ($_POST['module'] == 'clientes') {
                        $newRow = $this->setCustomer($dataToImport);
                    }

                    if ($_POST['module'] == 'colaboradores') {  
                        $newRow = $this->setUser($dataToImport);
                    }

                    if (!empty($newRow)) {
                        $items[] = $newRow['uuid'];
                        if (!empty($newRow['created']) && $newRow['created'] == true) {
                            $total += 1;
                        }
                    }
                }
            }

            if ($total > 0) {
                $uuid = $this->model->NewUUID();
                $_POST['uuid'] = $uuid;
                $_POST['user_uuid'] = $_SESSION['COD'];
                $_POST['total'] = $total;

                $crud = new Crud();
                $crud->setTable($this->model->getTable());
                $transaction = $crud->create($_POST);

                if ($transaction) {
                    $this->setImportItems($uuid, $items);
                    $this->toLog("Dados importados para a lista $uuid");
                    $data = [
                        'title' => 'Sucesso!',
                        'msg' => 'Dados Importados.',
                        'type' => 'success',
                        'pos' => 'top-right',
                        'uuid' => $uuid
                    ];
                } else {
                    $data = [
                        'title' => 'Erro!',
                        'msg' => 'Os Dados não foram importados.',
                        'type' => 'error',
                        'pos' => 'top-center'
                    ];
                }
            } else {
                $data = [
                    'title' => 'Dados Recebidos!',
                    'msg' => 'Página atualizada.',
                    'type' => 'success',
                    'pos' => 'top-right'
                ];
            }

            echo json_encode($data);
            return true;
        } else {
            return false;
        }
    }

    private function setCategory(string $category): array|bool
    {
        if (!empty($category)) {
            $fields = 'uuid, name, slug';
            $entity = $this->categoriesModel->getOneLikeParam($fields, 'name', $category);

            if (!$entity) {
                $entity = [
                    'uuid' => $this->categoriesModel->NewUUID(),
                    'name' => $category,
                    'slug' => $this->slugGenerator($category)
                ];

                $this->crudService($this->categoriesModel->getTable(), $entity);
                $entity['created'] = true;
            }

            return $entity;
        } else {
            return false;
        }
    }

    private function setSubcategory(string $subcategory, string $categoryUuid): array|bool 
    {
        if (!empty($subcategory) && !empty($categoryUuid)) {

            $fields = 'uuid, category_uuid, name, slug';
            $entity = $this->subcategoriesModel->getOneLikeParam($fields, 'name', $subcategory);
            if (!$entity) {
                $this->crudService($this->subcategoriesModel->getTable(), [
                    'uuid' => $this->subcategoriesModel->NewUUID(),
                    'category_uuid' => $categoryUuid,
                    'name' => $subcategory,
                    'slug' => $this->slugGenerator($subcategory)
                ]);
            }

            return $entity;
        } else {
            return false;
        }
    }

    private function setSubcategories(array $subcategories, string $categoryUuid): bool 
    {
        if (!empty($subcategories) && is_array($subcategories) && !empty($categoryUuid)) {
            foreach($subcategories as $subcategory) {
                $fields = 'uuid, category_uuid, name, slug';
                $entity = $this->subcategoriesModel->getOneLikeParam($fields, 'name', $subcategory);
                if (!$entity) {
                    $this->crudService($this->subcategoriesModel->getTable(), [
                        'uuid' => $this->subcategoriesModel->NewUUID(),
                        'category_uuid' => $categoryUuid,
                        'name' => $subcategory,
                        'slug' => $this->slugGenerator($subcategory)
                    ]);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function setCustomer(array $customer): array|bool
    {
        if (!empty($customer) && is_array($customer)) {
            if (!empty($customer[0]) && !empty($customer[1])) {
                $entity = $this->customersModel->getCustomerByEmail($customer[1]);
                if (!$entity) {
                    $password = $this->randomString();
                    $entity = [
                        'uuid' => $this->customersModel->NewUUID(),
                        'name' => $customer[0],
                        'email' => $customer[1],
                        'phone' => !empty($customer[2]) ? $customer[2] : null,
                        'cellphone' => !empty($customer[3]) ? $customer[3] : null,
                        'password' => Bcrypt::hash($password),
                        'postal_code' => !empty($customer[4]) ? $customer[4] : null,
                        'address' => !empty($customer[5]) ? $customer[5] : null,
                        'number' => !empty($customer[6]) ? $customer[6] : null,
                        'complement' => !empty($customer[7]) ? $customer[7] : null,
                        'neighborhood' => !empty($customer[8]) ? $customer[8] : null,
                        'city' => !empty($customer[9]) ? $customer[9] : null,
                        'state' => !empty($customer[10]) ? $customer[10] : null
                    ];
                    
                    $this->crudService($this->customersModel->getTable(), $entity);
                    $entity['created'] = true;
                }

                return $entity;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function setUser(array $user): bool|array
    {
        if (!empty($user) && is_array($user)) {
            if (!empty($user[0]) && !empty($user[3]) && !empty($user[4]) && !empty($user[8])) {
                $entity = $this->usersModel->getUserByEmail($user[8]);
                if (!$entity) {
                    $password = $this->randomString();
                    $entity = [
                        'uuid' => $this->usersModel->NewUUID(),
                        'name' => $user[0],
                        'document_1' =>  !empty($user[1]) ? $user[1] : null,
                        'document_2' =>  !empty($user[2]) ? $user[2] : null,
                        'job_role' => !empty($user[3]) ? $user[3] : null,
                        'role_uuid' => $this->getRoleUuid($user[4]),
                        'phone' => !empty($user[5]) ? $user[5] : null,
                        'cellphone' => !empty($user[6]) ? $user[6] : null,
                        'whatsapp' => !empty($user[7]) ? $user[7] : null,
                        'email' => $user[8],
                        'postal_code' => !empty($user[9]) ? $user[9] : null,
                        'address' => !empty($user[10]) ? $user[10] : null,
                        'number' => !empty($user[11]) ? $user[11] : null,
                        'complement' => !empty($user[12]) ? $user[12] : null,
                        'neighborhood' => !empty($user[13]) ? $user[13] : null,
                        'city' => !empty($user[14]) ? $user[14] : null,
                        'state' => !empty($user[15]) ? $user[15] : null,
                        'password' => Bcrypt::hash($password)
                    ];
                    
                    $this->crudService($this->usersModel->getTable(), $entity);
                    $entity['created'] = true;
                }

                return $entity;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function crudService(string $table, array $data): bool
    {
        if (!empty($table) && is_array($data)) {
            $crud = new Crud();
            $crud->setTable($table);
            $transaction = $crud->create($data);

            if ($transaction) {
                return true;
            }
        }

        return false;
    }

    private function setImportItems(string $parentUuid, array $items): bool
    {
        if (!empty($parentUuid) && !empty($items) && is_array($items)) {

            foreach($items as $item) {
                $this->crudService($this->itemsModel->getTable(), [
                    'parent_uuid' => $parentUuid,
                    'item_uuid' => $item
                ]);
            }

            return true; 
        } else {
            return false;
        }
    }

    private function getRoleUuid(string $role): string|bool
    {
        if (!empty($role)) {
            $entity = $this->roleModel->getOneLikeParam('uuid, name', 'name', $role);
            if (!$entity) {
                $roleUuid = $this->roleModel->NewUUID();
                $this->crudService($this->roleModel->getTable(), [
                    'uuid' => $roleUuid,
                    'name' => $role,
                    'is_admin' => '0'
                ]);

                $modules = $this->modulesModel->getAll();
                foreach ($modules as $module) {
                    if ($module['view_uuid'] != 0) {
                        $this->savePrivilege($roleUuid, $module['view_uuid'], $module['uuid']);
                    }

                    if ($module['create_uuid'] != 0) {
                        $this->savePrivilege($roleUuid, $module['create_uuid'], $module['uuid']);
                    }

                    if ($module['update_uuid'] != 0) {
                        $this->savePrivilege($roleUuid, $module['update_uuid'], $module['uuid']);
                    }

                    if ($module['delete_uuid'] != 0) {
                        $this->savePrivilege($roleUuid, $module['delete_uuid'], $module['uuid']);
                    }
                }

                $this->toLog("Cadastrou o perfil de acesso: $role #$roleUuid");

                return $roleUuid;
            } else {
                return $entity['uuid'];
            }
        } else {
            return false;
        }
    }

    private function savePrivilege($role, $resource, $module): void
    {
        $data = [
            'uuid' => $this->privilegesModel->NewUUID(),
            'role_uuid' => $role,
            'resource_uuid' => $resource,
            'module_uuid' => $module
        ];

        $crud = new Crud();
        $crud->setTable($this->privilegesModel->getTable());
        $crud->create($data);
    }
}
