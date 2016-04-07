<?php
/*ToDo
- шаблоны
- проверка прав function get_roots
- переименовать столбцы в нижнем регистре через _
*/
header('Content-Type: text/html; charset=utf-8');
//Вариант 11: метро
//Название, Линия, Тип, Среднее кол-во пассажиров в день, Средний доход в день
//Тип->пилонная, колонная, закрытого типа, односводчатая, двухярусная, однопролетная

function __autoload($class_name)
//Автозагрузка файла класса при его создании
{
	require_once("class/".$class_name.".php");
}

require_once('config.php');         //Конфиг 
$metro=new mysqldb($db_parameters); //Подключаемся к БД
//Подготовка {STATION_NAMES_RECORD}
$stations=$metro->get_stations();

if ($stations!='empty')
{
	$string='<select name="current_station_name" size="1">';
	for($i=1;$i<=sizeof($stations);$i++)
	{
		$string=$string."<option value='$stations[$i]'>$stations[$i]</option>\n";
	}
	$string=$string."</select>";
}
else
{
	$string="<p>БД пуста</p>\n";
}
$parse->set_tpl('{STATION_NAMES_RECORD}',$string);
#####^^^Этот кусок кода - отдельным файлом^^^###

######Голова HTML-документа######
require_once('head.php');
#################################



######Проверка авторизации######
if ((isset($_COOKIE['id']) and isset($_COOKIE['hash']))
	and (($_COOKIE['id'])!='' or ($_COOKIE['hash'])!='')  ) 
{  
//	echo "Cookie:<br>".$_COOKIE['id']."<br>".$_COOKIE['hash']."<br>";
	$userdata=$metro->get_users($_COOKIE['id']);
//	echo "DB:<br>".$userdata['user_id']."<br>".$userdata['user_hash']."<br>";
	
    if((($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id']))
	//or ($userdata['user_ip'] !== $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] !== "0")
	)
    {
        setcookie("id");
        setcookie("hash");
        print "Хм, что-то не получилось";
    }
	else
	{
	
		######Если пользователь выходит######
		if  (isset($_GET['exit']))
		{
			setcookie("id");
			setcookie("hash");
			unset($metro);
			header("Location: index.php");
		}
		
		######Если выбрали пункт меню######
		if (isset($_GET['p']))
		{
			$p=$_GET['p'];
			
			###Если выбрано "показать таблицу"###
			if ($p=='s')
			{
				include "show_stations.php";
			}
			
			###Если выбрано "добавить станцию"###
			elseif ($p=='a')
			{
				
				/*
				$parse = new template;
				for ($i=1;$i<$count;$i++){
					$parse->set_tpl($key[$i],$val[$i]);					
				}

				*/
				$parse->get_tpl('templates/add_station.tpl');
				$parse->tpl_parse();
				//Если данные не отправлялись
				if(!isset($_POST['station_name']))
				{
					print $parse->template;
				}
				//Если данные отправлялись
				else
				{
					$metrodata['station_name']=ucfirst($_POST['station_name']);
					$metrodata['line']=$_POST['line'];
					$metrodata['type']=$_POST['type'];
					$metrodata['avg_passengers']=+$_POST['avg_passengers'];
					$metrodata['avg_money']=+$_POST['avg_money'];
					
					foreach ($metrodata as $key=>$value)
					{
						$metrodata[$key]=htmlspecialchars($metrodata[$key]);
					}

					//Проверка правильности ввода пользователя
					$result=new antierror($metrodata);
					$error=$result->get_error();
					if (count($error) != 0) //Есть ошибка
					{
						foreach ($error as $show)
							echo "<font id='error'>$show</font><br>";		
						print $parse->template;
					}
					else
					//Проверка на ошибки SQL
					{
						//Выполняем SQL-запрос
						$result=$metro->add($metrodata,$userdata['user_login']);
						if($result[0]==true)
							echo "<font id='ok'>Добавлено!</font><br>";
						else
							echo "<font id='error'>$result[1]</font><br>";
						print $parse->template;
					}
				}
			}
			
			###Если выбрано "изменить станцию"###
			elseif ($p=='c')
			{
				$parse->get_tpl('templates/change_station.tpl');
				$parse->tpl_parse();
				//Если данные не отправлялись
				if(!isset($_POST['current_station_name']))
				{
					print $parse->template;
				}
				//Если данные отправлялись
				else
				{
					
					$metrodata['current_station_name']=$_POST['current_station_name'];
					$metrodata['station_name']=ucfirst($_POST['station_name']);
					$metrodata['line']=$_POST['line'];
					$metrodata['type']=$_POST['type'];
					$metrodata['avg_passengers']=+$_POST['avg_passengers'];
					$metrodata['avg_money']=+$_POST['avg_money'];
					
					foreach ($metrodata as $key=>$value)
					{
						$metrodata[$key]=htmlspecialchars($metrodata[$key]);
					}
					//Проверка правильности ввода пользователя
					$result=new antierror($metrodata);
					$error=$result->get_error();
					if (count($error) != 0) //Есть ошибка
					{
						foreach ($error as $show)
							echo "<font id='error'>$show</font><br>";		
						print $parse->template;
					}
					else
					{
						//Выполняем SQL-запрос
						$result=$metro->edit($metrodata);
						//$result=$metro->edit($metrodata,$userdata);
						if($result[0]==true)
							echo "<font id='ok'>Изменено!</font><br>";
						else
							echo "<font id='error'>$result[1]</font><br>";
						print $parse->template;                
					}
				}
			}
			
			###Если выбрано "удалить станцию"###
			elseif ($p=='d')
			{
				$parse->get_tpl('templates/delete_station.tpl');
				$parse->tpl_parse();
				//Если данные не отправлялись
				if(!isset($_POST['current_station_name']))
				{
					print $parse->template;
				}
				//Если данные отправлялись
				else
				{
					$metrodata['station_name']=htmlspecialchars($_POST['current_station_name']);
					//Проверка правильности ввода пользователя
					$result=new antierror($metrodata);
					$error=$result->get_error();
					if (count($error)!=0) //Есть ошибка
					{
						foreach ($error as $show)
							echo "<font id='error'>$show</font><br>";		
						print $parse->template;
					}
					else
					{
						//Выполняем SQL-запрос
						$result=$metro->del($metrodata);
						if($result[0]==true)
							echo "<font id='ok'>Удалено!</font><br>";
						else
							echo "<font id='error'>$result[1]</font><br>";
						print $parse->template;
					}
				}
			}
			//echo "<hr><a href='index.php'>Index</a>";
			//echo " <a href='index.php?exit'>Exit</a>";
			echo '
			<br>
			<ul class="bmenu">
			<hr>
			<li><a href="index.php">Index</a></li>
			<li><a href="index.php?exit">Exit</a></li>
			</ul>
				';
		}
		//Если зашли на главную
		else
		{
			echo '
			<h1>БД станций метро</h1>
			<br>
			<ul class="bmenu">
			<li><a href="index.php?p=s">Show Stations List</a></li>
			<li><a href="index.php?p=a">Add</a></li>
			<li><a href="index.php?p=c">Change</a></li>
			<li><a href="index.php?p=d">Delete</a></li>
			<li><a href="index.php?exit">Exit</a></li>
			</ul>
				';
			//echo '<hr><a href="index.php?exit"><p>Exit</p></a>';
		}
	}
}
else
{
	if(!isset($_GET['p']))
	{
		echo "<font id='error'>Вы не авторизованы</font><br>";
		include_once ('login.php');
		echo '<br><a href="index.php?p=r">Регистрация</a>';
	}
	else
	{
		if ($_GET['p']='r')
			include_once ('register.php');
	}
}
######Низ HTML-документа######
require_once('foot.php');
#################################
?>