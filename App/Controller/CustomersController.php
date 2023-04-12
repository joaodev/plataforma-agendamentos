<?php 

namespace App\Controller;

use Core\Controller\ActionController;
use Core\Di\Container;
use Core\Db\Crud;
use Core\Db\Bcrypt;
use PHPMailer;
use phpmailerException;

class CustomersController extends ActionController
{
	private mixed $model;

	public function __construct()
	{
		parent::__construct();
		$this->model = Container::getClass("Customers", "app");
	}

	public function indexAction(): void
	{
		@session_start();
        if (!empty($_SESSION['CND_COD']) && !empty($_SESSION['CND_TOKEN'])) {
            $this->render('index');
        } else {
            $this->render('login');
        }
	}

    public function userRegisterAction()
	{
		if (!empty($_POST)) {
            $queryString = (!empty($_POST['name']) ? "&name=" . $_POST['name'] : '');
            $queryString .= (!empty($_POST['email']) ? "&email=" . $_POST['email'] : '');

            if ($_POST['password'] != $_POST['confirmation']) {
                self::redirect('area-clientes', 'senhas-incorretas' . $queryString);
            } else {
                if (!$this->model->fieldExists('email', $_POST['email'], 'uuid')) {
                    unset($_POST['confirmation']);
                    
                    $uuid = $this->model->NewUUID();
                    $_POST['uuid'] = $uuid;

                    $_POST['password'] = Bcrypt::hash($_POST['password']);
                    $_POST['status'] = '1';

                    $code = $this->randomString();
                    $_POST['code'] = md5($code);

                    $crud = new Crud();
                    $crud->setTable($this->model->getTable());
                    $transaction = $crud->create($_POST);

                    if ($transaction) {
                        $config = $this->getSiteConfig();

                        $_SESSION['CND_COD']   = $uuid;
                        $_SESSION['CND_NAME']  = $_POST['name'];
                        $_SESSION['CND_EMAIL'] = $_POST['email'];
                        $_SESSION['CND_PASS']  = $_POST['password'];

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
                                                            <td style="text-align: center; padding:10px 0 10px 0;background:'.$config['primary_color'].';">
                                                                <h1 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">'.$config['site_title'].'</h1>
                                                                <h2 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">Token de Acesso</h2>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:36px 30px 42px 30px;">
                                                                <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                                    <tr>
                                                                        <td style="padding:0 0 10px 0;color:#153643;">
                                                                            <p>'.$_POST['name'].', tudo bem? </p>
                                                                            <p>Utilize este token para realizar o acesso:</p>
                                                                            <br>
                                                                            <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">'.$code.'</h1>
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
                                                            <td style="padding:30px;background:'.$config['primary_color'].';">
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
                        $mail->Host       = $config['mail_host'];
                        $mail->SMTPAuth   = true;                                 
                        $mail->Username   = $config['mail_username'];
                        $mail->Password   = $config['mail_password'];
                        $mail->Port       = $config['mail_port'];

                        try {
                            $mail->setFrom($config['mail_from_address'], utf8_decode($config['site_title']));
                        } catch (phpmailerException $e) {
                            $this->toLog("Erro ao definir destinatário: $e");
                        }

                        $mail->addAddress($_POST['email']);
            
                        $message = wordwrap($message, 70);
                        $mail->isHTML();                                  
                        $mail->Subject = utf8_decode('Token de Acesso');
                        $mail->Body    = utf8_decode($message);

                        try {
                            $mail->send();
                        } catch (phpmailerException $e) {
                            $this->toLog("Erro ao enviar: $e");
                        }

                        $this->toLog("{$_POST['name']} realizou o cadastro na plataforma.");
                    } else {
                        self::redirect('area-clientes', 'erro-interno' . $queryString);
                    } 
                }

                self::redirect('area-clientes/token');
            }
        } else {
            self::redirect('area-clientes');
        }
    }

    public function userLoginAction()
    {
        if (!empty($_POST)) {
            $email = $_POST['email'];
	        $password  = $_POST['password'];

	        $credentials = $this->model->findByCrenditials($email, $password); 
	        if ($credentials) {
                $code = $this->randomString();
                $crud = new Crud();
                $crud->setTable($this->model->getTable());
                $crud->update([
                    'code' => md5($code),
                    'code_validated' => '0'
                ], $credentials['uuid'], 'uuid');

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
                                                <td style="text-align: center; padding:10px 0 10px 0;background:'.$config['primary_color'].';">
                                                    <h1 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">'.$config['site_title'].'</h1>
                                                    <h2 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">Token de Acesso</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:36px 30px 42px 30px;">
                                                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                        <tr>
                                                            <td style="padding:0 0 10px 0;color:#153643;">
                                                                <p>'.$credentials['name'].', tudo bem? </p>
                                                                <p>Utilize este token para realizar o acesso:</p>
                                                                <br>
                                                                <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">'.$code.'</h1>
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
                                                <td style="padding:30px;background:'.$config['primary_color'].';">
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
                $mail->Host       = $config['mail_host'];
                $mail->SMTPAuth   = true;                                 
                $mail->Username   = $config['mail_username'];
                $mail->Password   = $config['mail_password'];
                $mail->Port       = $config['mail_port'];

                try {
                    $mail->setFrom($config['mail_from_address'], utf8_decode($config['site_title']));
                } catch (phpmailerException $e) {
                    $this->toLog("Erro ao definir destinatário: $e");
                }

                $mail->addAddress($credentials['email']);
    
                $message = wordwrap($message, 70);
                $mail->isHTML();                                  
                $mail->Subject = utf8_decode('Token de Acesso');
                $mail->Body    = utf8_decode($message);

                try {
                    $mail->send();
                } catch (phpmailerException $e) {
                    $this->toLog("Erro ao enviar: $e");
                }

                @session_start();
	        	$_SESSION['CND_COD']   = $credentials['uuid'];
	        	$_SESSION['CND_NAME']  = $credentials['name'];
	        	$_SESSION['CND_EMAIL'] = $credentials['email'];
	        	$_SESSION['CND_PASS']  = $credentials['password'];
	        	$_SESSION['CND_PHONE']  = $credentials['cellphone'];

                self::redirect('area-clientes/token');
	        } else {
                self::redirect('area-clientes', 'cliente-invalido');
            }
        } else {
            self::redirect('area-clientes');
        }
    }

    public function userTokenAction()
    {
        if (!empty($_POST) && !empty($_POST['token'])) {
            $customerUuid = $this->model->getUuidByField('code', $_POST['token'], 'uuid');
            if ($customerUuid) {
                $crud = new Crud();
                $crud->setTable($this->model->getTable());
                $validatedCode = $crud->update([
                    'code_validated' => '1',
                    'updated_at' => date('Y-m-d H:i:s')
                ], $customerUuid, 'uuid');
    
                if ($validatedCode) {
                    @session_start();
                    $_SESSION['CND_TOKEN'] = $_POST['token'];
                    $this->toLog("{$_SESSION['CND_NAME']} fez o login na plataforma.");

                    self::redirect('area-clientes');
                } else {
                    self::redirect('area-clientes/token', 'token-invalido');
                }
            } else {
                self::redirect('area-clientes/token', 'token-invalido');
            }
        } else {
            $this->render('token');
        }
    }

    public function userLogoutAction()
    {
        
        $this->toLog("{$_SESSION['CND_NAME']} fez o logout da plataforma.");

        @session_start();
		session_destroy();
		
		if (!empty($_SESSION['TOKENCART'])) {
			unset($_SESSION['TOKENCART']);
            
		}

		self::redirect('area-clientes/login');
    }

    public function profileAction()
	{
        @session_start();
        if (!empty($_POST)) {
            $uuidUser = $_SESSION['CND_COD'];
			if (!empty($_POST['name']) && $this->model->fieldExists('name', $_POST['name'], 'uuid', $uuidUser)) {
				self::redirect('area-clientes/meus-dados', 'nome-ja-cadastrado');
			} 

			if ($this->model->fieldExists('email', $_POST['email'], 'uuid', $uuidUser)) {
				self::redirect('area-clientes/meus-dados', 'email-ja-cadastrado');
			} 

			if (!empty($_POST['cellphone']) && $this->model->fieldExists('cellphone', $_POST['cellphone'], 'uuid', $uuidUser)) {
				self::redirect('area-clientes/meus-dados', 'celular-ja-cadastrado');
			}  

			if (!empty($_POST['password']) && !empty($_POST['confirmation'])) {
				if ($_POST['password'] != $_POST['confirmation']) {
	                self::redirect('area-clientes/meus-dados', 'senhas-incorretas');
	            } else {
					unset($_POST['confirmation']);
	                $_POST['password'] = Bcrypt::hash($_POST['password']);
	            }
			} else {
				unset($_POST['password']);
				unset($_POST['confirmation']);
			}
			
			$crud = new Crud();
			$crud->setTable($this->model->getTable());
			$transaction = $crud->update($_POST, $uuidUser, 'uuid');

			if ($transaction) {
				$_SESSION['CND_NAME']  = $_POST['name'];
	        	$_SESSION['CND_EMAIL'] = $_POST['email'];

	        	if (!empty($_POST['password'])) {
	        		$_SESSION['CND_PASS'] = $_POST['password'];
	        	}

				self::redirect('area-clientes/meus-dados', 'salvo');
			} else {
				self::redirect('area-clientes/meus-dados', 'erro');
			}
		} else {
            $entity = $this->model->getOne($_SESSION['CND_COD']);
			if ($entity) {
                $this->view->entity = $entity;
			    $this->render('profile');
            } else {
                self::redirect('area-clientes');
            }
		}
	}

    public function forgotPasswordAction()
    {
        if (!empty($_POST)) {
            $customerUuid = $this->model->getUuidByField('email', $_POST['email'], 'uuid');

            if ($customerUuid > 0) {
                $customer = $this->model->find($customerUuid, 'uuid, name, email', 'uuid');

                if ($customer) {        
                    $code = $this->randomString();

                    $crud = new Crud();
                    $crud->setTable($this->model->getTable());
                    
                    $updateCode = $crud->update([
                        'code' => md5($code),
                        'code_validated' => '0',
                        'updated_at' => date('Y-m-d H:i:s')
                    ], $customerUuid, 'uuid');

                    if ($updateCode) {
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
                                                            <td style="text-align: center; padding:10px 0 10px 0;background:'.$config['primary_color'].';">
                                                                <h1 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">'.$config['site_title'].'</h1>
                                                                <h2 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">Token de Acesso</h2>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:36px 30px 42px 30px;">
                                                                <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                                    <tr>
                                                                        <td style="padding:0 0 10px 0;color:#153643;">
                                                                            <p>'.$customer['name'].', tudo bem? </p>
                                                                            <p>Utilize este token para realizar o acesso:</p>
                                                                            <br>
                                                                            <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">'.$code.'</h1>
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
                                                            <td style="padding:30px;background:'.$config['primary_color'].';">
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
                        $mail->Host       = $config['mail_host'];
                        $mail->SMTPAuth   = true;                                 
                        $mail->Username   = $config['mail_username'];
                        $mail->Password   = $config['mail_password'];
                        $mail->Port       = $config['mail_port'];      
                        $mail->Debugoutput = true;

                        try {
                            $mail->setFrom($config['mail_from_address'], utf8_decode($config['site_title']));
                        } catch (phpmailerException $e) {
                            $this->toLog("Erro ao definir destinatário: $e");
                        }
                        
                        $mail->addAddress($customer['email']);
            
                        $message = wordwrap($message, 70);
                        $mail->isHTML();                                  
                        $mail->Subject = utf8_decode('Token de Acesso');
                        $mail->Body    = utf8_decode($message);

                        try {
                            $mail->send();
                        } catch (phpmailerException $e) {
                            $this->toLog("Erro ao enviar: $e");
                        }

                        $this->toLog("{$customer['name']} solicitou um código para recuperação de senha.");
                    }
                }
            }

            self::redirect('area-clientes/token-senha');
        } else {
            $this->render('forgot-password');
        }
    }

    public function passwordTokenAction()
    {
        if (!empty($_POST)) {
            $code = $_POST['token'];
            $customerUuid = $this->model->getUuidByField('code', $code, 'uuid');
            if ($customerUuid > 0) {
                $crud = new Crud();
                $crud->setTable($this->model->getTable());
                $validatedCode = $crud->update([
                    'code_validated' => '1',
                    'updated_at' => date('Y-m-d H:i:s')
                ], $customerUuid, 'uuid');
    
                if ($validatedCode) {
                    $this->view->code = $code;
                    $this->render('change-password');
                } else {
                    self::redirect('area-clientes/token-senha', 'token-invalido');
                }
            } else {
                self::redirect('area-clientes/token-senha', 'token-invalido');
            }
        } else {
            $this->render('password-token');
        }
    }

    public function changePasswordAction()
    {
        if (!empty($_POST)) {
            if (empty($_POST['info'])) {
                self::redirect('area-clientes/token-senha', 'token-invalido');
            } else {
                if ($_POST['password'] != $_POST['confirmation']) {
                    $this->view->code = $_POST['info'];
                    $this->view->errorPasswords = 'Senhas incorretas!';
                    $this->render('change-password');
                } else {
                    $customerUuid = $this->model->getUuidByField('code', $_POST['info'], 'uuid');
                    if ($customerUuid > 0) {
                        $customer = $this->model->find($customerUuid, 'uuid, email, password, code, code_validated', 'uuid');
                        if ($customer['code'] != $_POST['info'] || $customer['code_validated'] != '1') {
                            self::redirect('area-clientes/token-senha', 'token-invalido');
                        } else {
                            $crud = new Crud();
                            $crud->setTable($this->model->getTable());
                            
                            $newPassword = Bcrypt::hash($_POST['password']);
                            $updatedPassword = $crud->update([
                                'password' => $newPassword,
                                'code' => null,
                                'code_validated' => '0',
                                'updated_at' => date('Y-m-d H:i:s')
                            ], $customer['uuid'], 'uuid');
                            
                            if ($updatedPassword) {   
                                $credentials = $this->model->findByCrenditials($customer['email'], $_POST['password']); 

                                if ($credentials) {
                                    @session_start();
                                    $_SESSION['CND_COD']   = $credentials['uuid'];
                                    $_SESSION['CND_NAME']  = $credentials['name'];
                                    $_SESSION['CND_EMAIL'] = $credentials['email'];
                                    $_SESSION['CND_PASS']  = $credentials['password'];
                                    $_SESSION['CND_TOKEN'] = $_POST['info'];

                                    $this->toLog("{$credentials['name']} realizou a alteração de senha.");

                                    self::redirect('area-clientes');
                                } else {
                                    self::redirect('area-clientes', 'cliente-invalido');
                                }
                            } else {
                                self::redirect('area-clientes/token-senha', 'token-invalido');
                            }
                        }
                    } else {
                        self::redirect('area-clientes/token-senha', 'token-invalido');
                    }
                }
            }
        } else {
            self::redirect('area-clientes/token-senha', 'token-invalido');
        }
    }
}