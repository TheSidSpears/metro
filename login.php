<?php
// Страница авторизации
# Функция для генерации случайной строки
function generateCode($length=6)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";

    $clen = strlen($chars) - 1;  
    while (strlen($code) < $length)
            $code .= $chars[mt_rand(0,$clen)];  
    
    return $code;
}

if(isset($_POST['submit']))
{
    # Вытаскиваем из БД запись, у которой логин равняеться введенному
	$data=$metro->get_userinfo($_POST['login']);   

    # Сравниваем пароли
	if ($data==NULL)
	{
		echo "<font id='error'>Такого пользователя нет</font>";
	}
    else if($data['user_password'] === md5(md5($_POST['password'])))
    {
        # Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));

        if(!@$_POST['not_attach_ip'])
        {
            # Если пользователя выбрал привязку к IP
            # Переводим IP в строку
            //$insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
        }
        # Записываем в БД новый хеш авторизации и IP
		$metro->set_userinfo($hash/*,$insip*/,$data['user_id']);
        # Ставим куки
        setcookie("id", $data['user_id'], time()+60*60*24*30);
        setcookie("hash", $hash, time()+60*60*24*30);
        # Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: index.php"); exit();
    }
    else
        echo "<font id='error'>Вы ввели неправильный логин/пароль</font>";
}

?>
<form method="POST">
<font>Логин</font><br>
<input name="login" type="text"><br>
<font>Пароль</font><br>
<input name="password" type="password"><br>
<?php /* <font>Не прикреплять к IP(не безопасно)</font> <input type="checkbox" name="not_attach_ip"><br> */ ?>
<input name="submit" type="submit" value="Войти">
</form>