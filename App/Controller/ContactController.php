<?php

namespace App\Controller;

use Core\Controller\ActionController;
use PHPMailer;

class ContactController extends ActionController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction(): void
    {
        $this->render('index');
    }

    public function sendMessageAction()
    {
        $config = $this->getSiteConfig();

        if (!empty($_POST)) {
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
                                    <td align="center" style="padding:0;">
                                        <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                                            <tr>
                                                <td align="center" style="padding:10px 0 10px 0;background:' . $config['primary_color'] . ';">
                                                    <h1 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">' . $config['site_title'] . '</h1>
                                                    <h2 style="color: white; text-shadow: black 0.1em 0.1em 0.2em;">Fale Conosco</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:36px 30px 42px 30px;">
                                                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                                        <tr>
                                                            <td style="padding:0 0 10px 0;color:#153643;">
                                                                <h1>Uma pessoa entrou em contato pelo site! </h1>
                                                                <br>
                                                                <hr>
                                                                <p><b>Nome:</b> ' . $_POST['name'] . '</p>
                                                                <p><b>Email:</b> ' . $_POST['email'] . '</p>
                                                                <p><b>Assunto:</b> ' . $_POST['subject'] . '</p>
                                                                <hr>  
                                                                <p><b>Mensagem:</b> ' . $_POST['message'] . '</p>
                                                                <hr>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:30px;background:' . $config['primary_color'] . ';">
                                                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
                                                        <tr>
                                                            <td style="padding:0;width:50%;" align="left">
                                                               
                                                            </td>
                                                            <td style="padding:0;width:50%;" align="right">
                                                                <p style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
                                                                    &reg; ' . $config['site_title'] . '  | ' . date('Y') . '<br/>
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

            $mail->setFrom($config['mail_from_address'], utf8_decode($config['site_title']));
            $mail->addAddress($config['mail_to_address']);

            $message = wordwrap($message, 70);

            $mail->isHTML();
            $mail->Subject = utf8_decode('Contato recebido através do site');
            $mail->Body = utf8_decode($message);

            if ($mail->send()) {
                self::redirect('fale-conosco', 'enviado');
            } else {
                self::redirect('fale-conosco', 'nao-enviado');
            }
        } else {
            self::redirect('fale-conosco');
        }
    }
}