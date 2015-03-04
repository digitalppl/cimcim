<?php
/**
Plugin Name: Sberbank Equaring
 */

$pluginPath = plugin_dir_path(__FILE__);
include $pluginPath."include/class/TicketManage.php";
include $pluginPath."include/class/ApiSber.php";

add_action("widgets_init", function () {
    register_widget("RegisterFormWidget");
});


class RegisterFormWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("registerForm_widget", "Register form widget",
            array("description" => "Register form widget"));
    }

    public function widget($args, $instance) {
        $title = $instance["title"];
        $text = $instance["text"];

        if(get_locale() == "en_US")
        {
            $lang['name'] = "Name(required)";
            $lang['surname'] = "Surname (required)";
            $lang['work'] = "Place of work (required)";
            $lang['email'] = "Email (required)";
            $lang['phone'] = "Telephone number (required)";
            $lang['address'] = "Home address (required)";
            $lang['additionInfo'] = "Your Message";
            $lang['sapsan'] = "If you are planning a trip from Moscow to St. Petersburg, upload your passport via the form below. This is necessary for booking train Sapsan.";
            $lang['private_policy'] = "Read the following to learn more about our Privacy Policy";
            $lang['pay_btn'] = "Pay";

            $lang['required_field'] = "Required field";
            $lang['invalid_email'] = "Invalid email";
            $lang['confirm'] = "Необходимо подтвердить свое согласие с условиями Политики о конфиденциальности";
        }
        else
        {
            $lang['name'] = "Имя(обязательно)";
            $lang['surname'] = "Фамилия (обязательно)";
            $lang['work'] = "Место работы  (обязательно)";
            $lang['email'] = "Ваш e-mail (обязательно)";
            $lang['phone'] = "Телефон для связи (обязательно)";
            $lang['address'] = "Адрес проживания  (обязательно)";
            $lang['additionInfo'] = "Дополнительная информация";
            $lang['sapsan'] = "Если вы планируете поездку из Москвы в Санкт-Петербург, загрузите свой паспорт. Это необходимо для бронирования Сапсана.";
            $lang['private_policy'] = "Я согласен(на) с условиями Политики о конфиденциальности. Ознакомиться с Положением о конфиденциальности";
            $lang['pay_btn'] = "Оплатить";

            $lang['required_field'] = "Обязательное поле";
            $lang['invalid_email'] = "Неверный email";
            $lang['confirm'] = "Необходимо подтвердить свое согласие с условиями Политики о конфиденциальности";
        }

        if(!isset($_GET['event'])):
        ?>
        <script>
            $("document").ready(
                function()
                {
                    var message = {};

                    message['empty'] = "<?=$lang['required_field']?>";
                    message['email'] = "<?=$lang['invalid_email']?>"
                    message['confirm'] = "<?=$lang['confirm']?>"
                    $("#regForm").submit(
                        function()
                        {
                            var validate = true;
                            $("#regForm  input[name=firstname], input[name=lastname],  input[name=work], input[name=phone], input[name=email], input[name=address]").each(
                                function()
                                {
                                    if(!$(this).val())
                                    {
                                        $(this).addClass("error");
                                        $(this).parent("div").find(".errorText").text(message['empty']);
                                        validate = false;
                                    }
                                    else
                                    {
                                        $(this).removeClass();
                                        $(this).parent("div").find(".errorText").text("");
                                    }
                                }
                            );

                            $("#regForm  input[name=firstname], input[name=lastname],  input[name=work], input[name=phone], input[name=email], input[name=address]").keydown(
                                function()
                                {
                                    $(this).removeClass();
                                }
                            )

                            if(!/.*?@.*?/.test($("#regForm input[name=email]").val()))
                            {
                                $("#regForm input[name=email]").addClass("error");
                                $("#regForm input[name=email]").parent("div").find(".errorText").text(message['email']);
                                validate = false;
                            }
                            else
                            {
                                $("#regForm input[name=email]").removeClass();
                                $(this).parent("div").find(".errorText").text("");
                            }

                            if(validate)
                            {
                                if(!$("input[name=policyCheck]").is(":checked"))
                                {
                                    $("input[name=policyCheck]").parent("div").find(".errorText").text(message['confirm']);
                                    validate = false;
                                }
                            }



                            return validate;
                        }
                    );
                }
            );
        </script>
        <form action="/register-ajax.php" id="regForm" method="post" enctype="multipart/form-data">
                <div class="formItem"><?=$lang['name']?><br />
                        <input type="text" name="firstname"/>
                        <div class="errorText">

                        </div>
                </div>
                <div class="formItem"><?=$lang['surname']?><br />
                    <input type="text" name="lastname"/>
                    <div class="errorText">

                    </div>
                </div>

                        <div class="formItem"><?=$lang['work']?><br />
                            <input type="text" name="work"/>
                <div class="errorText">

                </div>
                        </div>

                        <div class="formItem"><?=$lang['email']?><br />
                            <input type="text" name="email"/>
                <div class="errorText">

                </div>
                        </div>

                        <div class="formItem"><?=$lang['phone']?><br />
                            <input type="text" name="phone"/>
                <div class="errorText">

                </div>
                        </div>

                        <div class="formItem"><?=$lang['address']?><br />
                            <input type="text" name="address"/>
                <div class="errorText">

                </div>
                        </div>

                        <div class="formItem"><?=$lang['additionInfo']?><br />
                            <textarea name="message"></textarea>
                <div class="errorText">

                </div>
                        </div>

                        <div class="formItem"><?=$lang['sapsan']?><br /><br />
                        <input type="file" name="file1"/><br /><br />
                        <input type="file" name="file2"/><br /><br />
                        <input type="file" name="file3"/></p>

                        <p><hr />
                        <input type="checkbox" name="policyCheck"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="http://cimcimrussia.com/privacy_statment/" target="_blank"><?=$lang['private_policy']?></a>.
                        <hr />
                            <div class="errorText">

                            </div>
                        </div>

                        <p><input type="submit" value="<?=$lang['pay_btn']?>"/>
                 </p>

        </form>
        <?elseif($_GET['event'] == "error"):?>
            <?if(isset($_GET['type']) && $_GET['type'] == "userAlreadyExists"):?>
                Участник с таким email уже зарегистрирован
                <?else:?>
                Отправка не удалась. Попробуйте позже.
            <?endif?>

            <?elseif($_GET['event'] == "success"):?>
            Заказ успешно оплачен. Билет будет отправлен Вам на email.
        <?endif;
    }
}
?>