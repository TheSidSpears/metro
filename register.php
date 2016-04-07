<?php
// Страница регситрации нового пользователя

if(isset($_POST['submit']))
{
    $error = array();
    # проверям логин

    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
        $error[] = "Логин может состоять только из букв английского алфавита и цифр";
    
    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
        $error[] = "Логин должен быть не меньше 3-х символов и не больше 30";

    # проверяем, не сущестует ли пользователя с таким именем

	
	$can_register=$metro->is_registered($_POST['login']);

	if ($can_register=false)
        $error[] = "Пользователь с таким логином уже существует в базе данных";

    # Если нет ошибок, то добавляем в БД нового пользователя

    if(count($error) == 0)
    {        
		$metro->register($_POST['login'],$_POST['password']);
        header("Location: index.php"); exit();
    }
    else
    {
        print "<b>При регистрации произошли следующие ошибки:</b><br>";
        foreach($error AS $value)
        {
            print "<font color='red'>".$value."</font><br>";
        }
    }
}
?>

<form method="POST">
<font>Логин</font><br>
<input name="login" type="text"><br>
<font>Пароль</font><br>
<input name="password" type="password"><br>
<input name="submit" type="submit" value="Зарегистрироваться">
<br><a href="index.php">Авторизоваться</a>
</form>