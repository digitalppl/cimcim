<?php
/**
 * Created by PhpStorm.
 * User: evgenius
 * Date: 2/19/15
 * Time: 1:40 AM
 */

require( dirname(__FILE__) . '/wp-load.php' );
$api = new ApiSber();

$amount = 10000;

if($_REQUEST['action'] == "pay_success")
{
    $orderNumber = $_GET['orderNumber'];
    $orderId = $_GET['orderId'];
    $resp = $api->getOrderStatus($orderId, $orderNumber, $amount);
    if($resp->OrderStatus == 2)
    {
        $user = get_userdata($orderNumber);
        require_once ABSPATH . WPINC . '/class-phpmailer.php';

        $mailer = new PHPMailer();
        $mailer->setFrom("ticket@cimcimrussia.com");
        $mailer->addAddress($user->data->user_email);
        $mailer->Body = "Заявка на конференцию";
        $mailer->Subject = 'Билет на конференцию';
        $mailer->CharSet = "UTF-8";
        $mailer->send();


        $metaData = get_user_meta($orderNumber, "data");
        $metaData = $metaData[0];

        $body = file_get_contents(WP_CONTENT_DIR."/plugins/sberbank/mailTpl/request.html");

        foreach($metaData as $k => $v)
        {
            $body = str_replace("#".$k."#", $v, $body);
        }

        $mailer = new PHPMailer();
        $mailer->setFrom("ticket@cimcimrussia.com");
        $mailer->addAddress("international@glinka.museum");
        $mailer->Body = $body;
        $mailer->Subject = 'Регистрация CimcimRussia';
        $mailer->CharSet = "UTF-8";


        $metaFiles = get_user_meta($orderNumber, "files");
        $metaFiles = $metaFiles[0];
        foreach($metaFiles as $filename)
        {
            $mailer->addAttachment($filename);
        }
        $mailer->send();

        wp_redirect("/registration?event=success");

    }
    else
    {
        wp_redirect("/registration?event=error");
    }
}
else
{
    $postData = $_POST;

    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    $max=10;
    $size=StrLen($chars)-1;
    $password="";
    while($max--)
        $password.=$chars[rand(0,$size)];


    $user_id = wp_insert_user(
        array(
            "user_login" => $postData['email'],
            "user_pass" => $password,
            "first_name" => $postData['firstname'],
            "last_name" => $postData['lastname'],
            "user_email" => $postData['email'],
            "description" => sprintf(
                "Работа: %s\nТелефон: %s\nДомашний адрес: %s\nДополнительная информация: %s",
                $postData['work'],
                $postData['phone'],
                $postData['address'],
                $postData['message']
            )
        )
    );


    if(is_int($user_id))
    {
        $types['image/jpeg'] = "jpg";
        $types['image/png'] = "png";
        $types['image/gif'] = "gif";

        foreach($_FILES as $k => $v)
        {
            if(isset($types[$v['type']]))
            {
                $ext = $types[$v['type']];
                $filename = WP_CONTENT_DIR."/uploads/sberbank/".uniqid().".".$ext;

                if(copy($v['tmp_name'], $filename))
                    $files[] = $filename;
            }
        }

        add_user_meta($user_id, "files", $files);
        add_user_meta($user_id, "data", $postData);
        $resp = $api->register($user_id, $amount, get_site_url()."/register-ajax.php?action=pay_success&orderNumber=".$user_id);
        wp_redirect($resp->formUrl);
    }
    else
    {
        wp_redirect("/registration?event=error&type=userAlreadyExists");
    }
}







