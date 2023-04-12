<?php

namespace Core\Controller;

use Core\Db\SecurityFilter;
use Core\Db\Logs;
use Core\Di\Container;
use Core\Db\Crud;
use stdClass;
use PHPMailer;
use phpmailerException;

class ActionController
{
    protected mixed $action;
    protected mixed $namespace;
    protected mixed $controller;
    protected mixed $view;

    public function __construct()
    {
        @session_start();

        if (!empty($_GET)) {
            self::dataValidation($_GET);
        }

        if (!empty($_POST)) {
            if (
                empty($_POST['description']) && empty($_POST['description_1']) && empty($_POST['description_2'])
                && empty($_POST['mail_password']) && empty($_POST['password']) && empty($_POST['confirmation'])
                && empty($_POST['footer_description'])
            ) {
                self::dataValidation($_POST);
            }
        }

        $this->view = new stdClass();
    }

    public function indexAction(): void
    {
        $this->render('index', false);
    }

    public function render($action, $layout = true): void
    {
        $this->action = $action;

        $current = get_class($this);
        $exp_current_class = explode("\\", $current, 3);

        $this->namespace = $exp_current_class[0];
        $this->controller = strtolower($exp_current_class[2]);

        if ($layout == true && file_exists("../" . $this->namespace . "/view/layout/layout.phtml")) {
            include_once("../" . $this->namespace . "/view/layout/layout.phtml");
        } else {
            $this->content();
        }
    }

    public function content(): void
    {
        $class_name = str_replace("controller", "", $this->controller);
        include_once('../' . $this->namespace . '/view/' . $class_name . '/' . $this->action . '.phtml');
    }

    public static function redirect($module, $msgCallback = null): void
    {
        $callback = "";

        if (!empty($msgCallback)) {
            $callback .= "?msg=" . $msgCallback;
        }

        header("Location: " . baseUrl . $module . $callback);
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function randomString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randstring = '';
        for ($i = 0; $i < 6; $i++) {
            $randstring .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randstring;
    }

    public function formatDate($input_date): string
    {
        if (!empty($input_date)) {
            $exp = explode("-", $input_date, 3);
            $date = $exp[2] . '/' . $exp[1] . '/' . $exp[0];
            return $date;
        } else {
            return "";
        }
    }

    public function formatDateTime($input_date_time, $time = true): string
    {
        if (empty($input_date_time)) {
            return '';
        } else {
            $exp_dt = explode(" ", $input_date_time, 2);
            $exp_date = explode("-", $exp_dt[0], 3);
            $date = $exp_date[2] . '/' . $exp_date[1] . '/' . $exp_date[0];

            if ($time == true) {
                $exp_time = explode(":", $exp_dt[1], 3);
                return $date . ' ' . $exp_time[0] . ':' . $exp_time[1];
            } else {
                return $date;
            }
        }
    }

    public function formatDateTimeWithLabels($input_date_time, $time = true): string
    {
        if (empty($input_date_time)) {
            return $input_date_time;
        } else {
            $exp_dt = explode(" ", $input_date_time, 2);
            $exp_date = explode("-", $exp_dt[0], 3);
            $date = $exp_date[2] . '/' . $exp_date[1] . '/' . $exp_date[0];

            if ($time == true) {
                $exp_time = explode(":", $exp_dt[1], 3);
                return $date . ' às ' . $exp_time[0] . ':' . $exp_time[1];
            } else {
                return $date;
            }
        }
    }

    public function moneyToDb($value): array|string
    {
        $value = trim($value);
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);
        return $value;
    }

    public static function dataValidation($data): void
    {
        $securityFilter = new SecurityFilter();
        foreach ($data as $item) {
            if (!$securityFilter->secureData($item)) {
                session_destroy();
                self::redirect('');
            }
        }
    }

    public function toLog($msg): bool|string|null
    {
        $log = new Logs();
        return $log->toLog($msg);
    }

    public function inputMasked($val, $mask): string
    {
        $maskared = '';
        if (!empty($val)) {
            $k = 0;
            for ($i = 0; $i <= strlen($mask) - 1; $i++) {
                if ($mask[$i] == '#') {
                    if (isset($val[$k])) {
                        $maskared .= $val[$k++];
                    }
                } else {
                    if (isset($mask[$i])) {
                        $maskared .= $mask[$i];
                    }
                }
            }
        }

        return $maskared;
    }

    public function dateToTimestamp($data): bool|int
    {
        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);
        return mktime(0, 0, 0, $mes, $dia, $ano);
    }

    public function acl($roleUuid, $resourceUuid, $moduleUuid)
    {
        $aclModel = Container::getClass("Acl", "admin");
        return $aclModel->getGrantedPrivilege($_SESSION['COD'], $roleUuid, $resourceUuid, $moduleUuid);
    }

    public function getSiteConfig()
    {
        $configModel = Container::getClass("Config", "admin");
        $configData = $configModel->getOne();

        return $configData;
    }

    public function getUser($uuid)
    {
        $userModel = Container::getClass("User", "admin");
        $userData = $userModel->getOne($uuid);
        return $userData;
    }

    public function slugGenerator($title): array|string|null
    {
        $slug = preg_replace('/[\@\.\;\"]+/', '', $title);
        $slug = str_replace(" ", "-", $slug);
        $slug = str_replace("&", "", $slug);
        $slug = str_replace("ç", "c", $slug);
        $slug = str_replace("Ç", "c", $slug);
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("'", "", $slug);
        $slug = str_replace(". ", "", $slug);
        $slug = strtolower($slug);

        return preg_replace(
            array(
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/"
            ),
            explode(" ", "a A e E i I o O u U n N"),
            $slug
        );
    }

    public function removeSpecialChars($string): string
    {
        $slug = ($string);
        $slug = str_replace("ç", "c", $slug);
        $slug = str_replace("Ç", "c", $slug);

        $slug = preg_replace(
            array(
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/"
            ),
            explode(" ", "a A e E i I o O u U n N"),
            $slug
        );

        return strtoupper($slug);
    }

    public function formatCellphone($cellphone): ?string
    {
        if (!empty($cellphone)) {

            $cellphone = str_replace('(', '', $cellphone);
            $cellphone = str_replace(')', '', $cellphone);
            $cellphone = str_replace('-', '', $cellphone);

            return '55' . trim($cellphone);
        } else {
            return null;
        }
    }

    public function resourceCodes($key): string
    {
        $codes = [
            'view' => 'bd2cc1d6-712c-ec21-cef3-e634a1d78c28',
            'create' => '9f4aaeb8-3fd2-5d53-53f7-3547596d06d2',
            'update' => 'fd52263b-5fe3-185b-c06f-5344e2eba9c0',
            'delete' => '82fa103f-2628-9c51-7063-ef2aabe7afa4'
        ];

        return $codes[$key];
    }

    public function moduleCodes($key): string
    {
        $codes = [
            'user' => '92050339-fffe-6bf0-a10b-b2e0b7cb5a86',
            'privileges' => '60e112b8-c2d7-12e1-3106-084f31537b6c',
            'configs' => 'c315421e-af07-aa52-8c8e-715a511be94d',
            'logs' => 'a985e89b-ca02-cf03-5080-c81b8578709b',
            'seo' => '4a779c2b-1864-e061-9c12-615f9466b5df',
            'home' => '339e74f5-8fb9-4570-86e6-f40c5e30462b',
            'banners' => '9f0004a9-a9bb-411d-839c-35223d0b82af',
            'internal-banners' => '5c6cc90b-3d84-424d-81d9-3f421abe28e4',
            'about' => 'aca6f96f-1447-4b5f-b799-f41993322256',
            'blog' => '7b7bde62-e611-411c-af9e-26dc8aa0ed40',
            'testimonials' => 'c26cc26f-ad63-4ee8-9a34-f841049af3e8',
            'newsletter' => 'a55ed7dd-4269-4a15-b0c8-0fa1c5cb7aca',
            'categories' => 'f99ddfe1-40fe-a71c-276a-479b6a641f94',
            'subcategories' => 'e43c267e-5898-40b6-910a-53b8f72bc7a6',
            'customers' => '71ebf4ef-280f-4924-9cb5-1b7a55935c80',
            'smtp' => '2d1503eb-46bc-5386-957a-ec2def778c4e',
            'services' => '62430c22-1a3d-42ff-8921-fca074a676dc',
            'politics' => 'eccd0f74-2c1a-3600-fafb-ff14d65b0160',
            'use-terms' => '5db9cbc7-a131-445e-9a83-22801209ef8f',
            'importer' => '2bead19f-2994-4be7-822b-3a5db2bb4d50',
        ];

        return $codes[$key];
    }

    public function getSiteSeo()
    {
        $seoModel = Container::getClass("Seo", "admin");
        $seoData = $seoModel->getOne();

        return $seoData;
    }

    public function getHomeConfig()
    {
        $homeModel = Container::getClass("Home", "admin");
        $homeData = $homeModel->getOne();

        return $homeData;
    }

    public function checkBlogPosts()
    {
        $blogModel = Container::getClass("Blog", "admin");
        $blog = $blogModel->getAll(null);

        if (count($blog) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getInternalBanner($type): string
    {
        $model = Container::getClass("InternalBanners", "admin");
        $data = $model->getOne();

        if ($data && !empty($data[$type])) {
            return baseUrl . 'public/uploads/banner/' . $data[$type];
        } else {
            return baseUrl . 'public/app/assets/images/padrao.jpg';
        }
    }

    public function getPostFile($uuid): ?string
    {
        $model = Container::getClass("Files", "admin")
            ->findActive($uuid, 'file', 'parent_uuid');

        if (!empty($model)) {
            return $uuid . '/' . $model['file'];
        } else {
            return null;
        }
    }

    public function getCurrentPost($slug): array
    {
        $blogModel = Container::getClass("Blog", "admin");
        $blog = $blogModel->getOneBySlug($slug);

        if (!empty($blog)) {
            return $blog;
        } else {
            $seo = $this->getSiteSeo();
            return [
                'title' => $seo['title'],
                'description' => $seo['description']
            ];
        }
    }

    public function getCurrentService($slug): array
    {
        $servicesModel = Container::getClass("Services", "admin");
        $services = $servicesModel->getOneBySlug($slug);

        if (!empty($services)) {
            return $services;
        } else {
            $seo = $this->getSiteSeo();
            return [
                'title' => $seo['title'],
                'description' => $seo['description']
            ];
        }
    }

    public function totalSubcategories($category_uuid): string
    {
        $subModel = Container::getClass("Subcategories", "admin");
        return $subModel->getTotalByCategory($category_uuid);
    }

    public function sendMail(array $data): bool
    {
        if (
            !empty($data['title']) && !empty($data['message'])
            && !empty($data['name']) && !empty($data['toAddress'])
        ) {

            $config = $this->getSiteConfig();
            $message = '<!DOCTYPE html>
                <html lang="en" xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width,initial-scale=1">
                    <meta name="x-apple-disable-message-reformatting">
                    <title></title>
                    <!--[if mso]>
                    <noscript>
                        <xml>
                            <o:OfficeDocumentSettings>
                                <o:PixelsPerInch>96</o:PixelsPerInch>
                            </o:OfficeDocumentSettings>
                        </xml>
                    </noscript>
                    <![endif]-->
                    <style>
                        table, td, div, h1, p {font-family: Arial, sans-serif;}
                    </style>
                </head>
                <body style="margin:0;padding:0;">
                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
                        <tr>
                            <td style="text-align: center; padding:0;">
                                <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                                    <tr>
                                        <td style="text-align: center; padding:10px 0 10px 0;background:' . $config['primary_color'] . ';">
                                            <h1 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">' . $config['site_title'] . '</h1>
                                            <h2 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">' . $data['title'] . '</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:36px 30px 42px 30px;">
                                            <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                <tr>
                                                    <td style="padding:0 0 10px 0;color:#153643;">
                                                        <p>' . $data['name'] . ', tudo bem? </p>
                                                        ' . $data['message'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:0;">
                                                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                            <tr>
                                                                <td style="width:260px;padding:0;vertical-align:top;color:#153643;">
                                                                    <p style="margin:0 0 12px 0;font-size:11px;line-height:15px;font-family:Arial,sans-serif;">*Esta é uma mensagem automática, não responda este email.</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:30px;background:' . $config['primary_color'] . ';">
                                            <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
                                                <tr>
                                                    <td style="padding:0;width:100%; text-align: center;">
                                                        <p style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
                                                            &copy; ' . $config['site_title'] . '  | ' . date('Y') . '<br/>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $config['mail_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['mail_username'];
            $mail->Password = $config['mail_password'];
            $mail->Port = $config['mail_port'];

            try {
                $mail->setFrom($config['mail_from_address'], utf8_decode($config['site_title']));
            } catch (phpmailerException $e) {
                $this->toLog("Erro ao definir destinatário: $e");
                return false;
            }

            $mail->addAddress($data['toAddress']);

            $message = wordwrap($message, 70);
            $mail->isHTML();
            $mail->Subject = utf8_decode($data['title']);
            $mail->Body = utf8_decode($message);

            try {
                $mail->send();
                return true;
            } catch (phpmailerException $e) {
                $this->toLog("Erro ao enviar: $e");
                return false;
            }
        } else {
            return false;
        }
    }

    public function setWatermark($dir): void
    {
        if (!empty($dir)) {
            $config = $this->getSiteConfig();
            if (!empty($config['logo_watermark'])) {
                $imageFile = $dir;
                $watermarkFile = "../public/uploads/logo/{$config['logo_watermark']}";
                $imageExt = pathinfo($imageFile);
                $imageExt = $imageExt['extension'];

                if ($imageExt == 'jpg' || $imageExt == 'jpeg') {
                    $image = @imagecreatefromjpeg($imageFile);
                }

                if ($imageExt == 'png') {
                    $image = @imagecreatefrompng($imageFile);
                }

                if ($image) {
                    $watermark = imagecreatefrompng($watermarkFile);
                    $watermarkWidth = imagesx($watermark);
                    $watermarkHeight = imagesy($watermark);

                    $imageWidth = imagesx($image);
                    $imageHeight = imagesy($image);

                    $positionX = ($imageWidth / 2) - ($watermarkWidth / 2);
                    $positionY = ($imageHeight / 2) - ($watermarkHeight / 2);

                    imagecopy(
                        $image,
                        $watermark,
                        (int) $positionX,
                        (int) $positionY,
                        0,
                        0,
                        $watermarkWidth,
                        $watermarkHeight
                    );

                    header('Content-type: image/png');
                    imagepng($image, $dir);
                    imagedestroy($image);
                    imagedestroy($watermark);
                }
            }

        }
    }

    public function totalActiveServices(): string
    {
        $model = Container::getClass("Services", "admin");
        return $model->totalActiveServices();
    }

    public function totalActivePublications(): string
    {
        $model = Container::getClass("Blog", "admin");
        return $model->totalActivePublications();
    }
}