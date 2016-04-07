<?php
interface view
{
	#Работа с даннымми БД#
	function add($data,$login);//Добавить запись в таблице
	function edit($data);//Изменить запись в таблице
	function del($data);//Удалить запись в таблице
	function get_stations();//Отправляет список станций
	#Авторизация пользователей#
	function get_users($id);//Отправляет список пользователей
	function get_userinfo($login);//Отправляет логин/пароль конкретного юзера
	function set_userinfo($hash/*,$insip*/,$user_id);//Проводит авторизацию юзера
	#Регистрация пользователей#
	function is_registered($login);//Проверяет, есть ли уже такой логин
	function register($login,$password);//Регистрирует пользователя
	#Назначение прав на запись#
	function set_roots($auth,$login);//Устанавливает права
	//function get_roots($auth,$login);
	#Шаблоны
	function get_template($temp,$lang);//Отправляет текстовое значение вместо шаблона, в зависимости от языка
}


class mysqldb implements view
{
    protected $db;
    protected $param;
	protected $stations;
###############################################################################################################
    function __construct($db_parameters)
    //Конструктор. Подключение к бд
    {
        $this->param=$db_parameters;
        
        $connect_str = $this->param['DB_DRIVER']
            .':host='. $this->param['DB_HOST']
            .';dbname='.$this->param['DB_NAME'];
        $this->db = new PDO($connect_str,$this->param['DB_USER'],$this->param['DB_PASS'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }
##############################################Вспомогательные функции####################################################    
###############################################################################################################	
	function pdo_exec($rows,$func_name)
	//Выполняет SQL-запрос или возвращает ошибку
	{
		if (!$rows->execute())
		{
		  echo "error in function $func_name: ".__CLASS__;
		}		
	}
##############################################Вывод таблицы####################################################    
###############################################################################################################
	function get_columns()
	//Возвращает список полей основной таблицы
	{			
		$rows = $this->db->prepare("SELECT `Field` FROM (SHOW COLUMNS FROM `".$this->param['TABLE_NAME']."`)");
		//pdo_exec($rows);
		$this->pdo_exec($rows,__FUNCTION__);
		//Подсчитаем кол-во записей
		$count_rows= $this->db->prepare("SELECT count(*) FROM (SHOW COLUMNS FROM `".$this->param['TABLE_NAME']."`)");
		if (!$count_rows->execute())
		{
		  echo 'error: '.__CLASS__;
		}
		$count_rows=$count_rows->fetch(PDO::FETCH_ASSOC);
		var_dump($count_rows);
		foreach($count_rows as $key=>$value)
			$count_rows=$value;
		//Поместим содержимое в массив
		if ($count_rows!=0)
		{
			for($i=1;$i<=$count_rows;$i++)
			{
				$result=$rows->fetch(PDO::FETCH_ASSOC);
				foreach($result as $key=>$value)
					$columns[$i]=$value;	
			}
			//$this->stations=$stations;
		}
		else
		{
			$columns='empty';
		}
		$result['count']=$count_rows;
		$result['columns']=$columns;
		
		return $result;			
	}

###############################################################################################################
	function get_table()
	{
		$rows = $this->db->prepare("SELECT * FROM `".$this->param['TABLE_NAME']."`");
		$this->pdo_exec($rows,__FUNCTION__);
		
		$result=$rows->fetchAll(PDO::FETCH_ASSOC);
		//var_dump($result);
		//foreach($result as $key=>$value)
		//	$table[$i]=$value;
		var_dump($result);
		return $result;
	}
#######################################Работа с даннымми БД####################################################    
###############################################################################################################    

    function add($data,$login)
    //Добавить запись в таблице
	//$data - содержимое записи
	//$login - логин пользователя
    {
		$already_added=$this->db->prepare("SELECT count(*) FROM `".$this->param['TABLE_NAME']."`
										   WHERE `Station Name`=:station_name AND`Line`=:line");
        $already_added->bindValue(':station_name', $data['station_name'], PDO::PARAM_STR);
        $already_added->bindValue(':line', $data['line'], PDO::PARAM_STR);
		$this->pdo_exec($already_added,__FUNCTION__);
		
		$already_added=$already_added->fetch(PDO::FETCH_ASSOC);
		
		
		foreach($already_added as $key=>$value)
			$already_added=$value;

			
		if ($already_added==0) //Если нет такой же станции на этой ветке
		{
		
			$rows = $this->db->prepare("INSERT INTO `".$this->param['TABLE_NAME']."`
								(`Station Name`,`Line`,`Type`,`Passengers`,`Money`)
								VALUES (:station_name,:line,:type,:avg_passengers,:avg_money)
					");
			$rows->bindValue(':station_name', $data['station_name'], PDO::PARAM_STR);
			$rows->bindValue(':line', $data['line'], PDO::PARAM_STR);
			$rows->bindValue(':type', $data['type'], PDO::PARAM_STR);
			$rows->bindValue(':avg_passengers', $data['avg_passengers'], PDO::PARAM_INT);
			$rows->bindValue(':avg_money', $data['avg_money'], PDO::PARAM_INT);
			$this->pdo_exec($rows,__FUNCTION__);		
			
			//Если есть SQL-ошибка
			$error_array = $this->db->errorInfo();
			if($this->db->errorCode() != 0000)
			{
				$welldone[0]=false;
				$welldone[1]="SQL ошибка: ".$error_array[2];
			}	
			//Получаем id записи
			$get_id = $this->db->prepare("SELECT id FROM `".$this->param['TABLE_NAME']."`
										WHERE `Station Name`=:station_name AND `Line`=:line");
			$get_id->bindValue(':station_name', $data['station_name'], PDO::PARAM_STR);
			$get_id->bindValue(':line', $data['line'], PDO::PARAM_STR);
			$this->pdo_exec($get_id,__FUNCTION__);
			
			$id=$get_id->fetchAll(PDO::FETCH_ASSOC);
			$id=$id[0]['id'];//приводим в нормальный вид
			
			//Если запрос проведен
			if($rows)
			{
				$welldone[0]=true;
				//устанавливаем права
				$this->set_roots($login,$id);
			}
	
		}
		else //Если на этой ветке существует такая станция
		{
			$welldone[0]=false;
			$welldone[1]="Такая станция уже существует на этой ветке";
		}
		return $welldone;
    }
    
###############################################################################################################    
    
    function edit($data)
    //Изменить запись в таблице
    {
        $rows = $this->db->prepare("UPDATE `".$this->param['TABLE_NAME']."` SET
						`Station Name`=:station_name,
						`Line`=:line,
						`Type`=:type,
						`Passengers`=:avg_passengers,
						`Money`=:avg_money
						WHERE `Station Name`=:current_station_name
				");
				//WHERE `id`=:id
        $rows->bindValue(':station_name', $data['station_name'], PDO::PARAM_STR);
        $rows->bindValue(':line', $data['line'], PDO::PARAM_STR);
        $rows->bindValue(':type', $data['type'], PDO::PARAM_STR);
        $rows->bindValue(':avg_passengers', $data['avg_passengers'], PDO::PARAM_INT);
        $rows->bindValue(':avg_money', $data['avg_money'], PDO::PARAM_INT);
		$rows->bindValue(':current_station_name', $data['current_station_name'], PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);
		
        //Если есть SQL-ошибка
		$error_array = $this->db->errorInfo();
        if($this->db->errorCode() != 0000)
		{
			$welldone[0]=false;
			$welldone[1]="SQL ошибка: ".$error_array[2];
		}
        //Если запрос проведен
        if($rows)
        {
            $welldone[0]=true;
        }
        
        return $welldone;    
    }
    
###############################################################################################################    
    
    function del($data)
	//Удалить запись в таблице
    {
        $rows = $this->db->prepare("DELETE FROM `".$this->param['TABLE_NAME']."` WHERE `Station Name`=:station_name");
        $rows->bindValue(':station_name', $data['station_name'], PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);
        //Если есть SQL-ошибка
		$error_array = $this->db->errorInfo();
        if($this->db->errorCode() != 0000)
		{
			$welldone[0]=false;
			$welldone[1]="SQL ошибка: ".$error_array[2];
		}
        //Если запрос проведен
        if($rows)
        {
            $welldone[0]=true;
        }
        
        return $welldone;
    }

###############################################################################################################
	function get_stations()
	//Отправляет список станций
	//Сделать дополнение: отправляет список станций, которые создал текущий юзер
	{
		$rows = $this->db->prepare("SELECT `Station Name` FROM `".$this->param['TABLE_NAME']."`");
		$this->pdo_exec($rows,__FUNCTION__);
		
		$count_rows= $this->db->prepare("SELECT count(*) FROM `".$this->param['TABLE_NAME']."`");
		$this->pdo_exec($count_rows,__FUNCTION__);

		$count_rows=$count_rows->fetch(PDO::FETCH_ASSOC);
		foreach($count_rows as $key=>$value)
			$count_rows=$value;
		//Поместим содержимое в массив
		if ($count_rows!=0)
		{
			for($i=1;$i<=$count_rows;$i++)
			{
				$result=$rows->fetch(PDO::FETCH_ASSOC);
				foreach($result as $key=>$value)
					$stations[$i]=$value;	
			}
			$this->stations=$stations;
		}
		else
		{
			$this->stations='empty';
		}
		return $this->stations;
	}
	
#######################################Авторизация пользователей###############################################
###############################################################################################################
	function get_users($id)
	//Отправляет список пользователей
	{
		$rows=$this->db->prepare("SELECT *,INET_NTOA(user_ip) FROM ".$this->param['TABLE_USERS']." WHERE user_id = :cookie_id LIMIT 1");
		$rows->bindValue(':cookie_id', intval($id), PDO::PARAM_INT);
		$this->pdo_exec($rows,__FUNCTION__);
		
		$userdata=$rows->fetchAll(PDO::FETCH_ASSOC);
		$userdata=$userdata[0];//приводим в нормальный вид
		return $userdata;
		
	}
###############################################################################################################
	function get_userinfo($login)
	//Отправляет логин/пароль конкретного юзера
	{
		$rows=$this->db->prepare("SELECT user_id, user_password FROM ".$this->param['TABLE_USERS']." WHERE user_login=:login LIMIT 1");
		$rows->bindValue(':login', $login, PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);

		if ($log_pass=$rows->fetchAll(PDO::FETCH_ASSOC))
			$log_pass=$log_pass[0];//приводим в нормальный вид
		else
			$log_pass=NULL;
		return $log_pass;
	}
###############################################################################################################
	function set_userinfo($hash/*,$insip*/,$user_id)
	//Проводит авторизацию юзера
	{
		//$rows=$this->db->prepare("UPDATE ".$this->param['TABLE_USERS']." SET user_hash=:hash :insip WHERE user_id=:user_id");
		$rows=$this->db->prepare("UPDATE ".$this->param['TABLE_USERS']." SET user_hash=:hash WHERE user_id=:user_id");
		$rows->bindValue(':hash', $hash, PDO::PARAM_STR);
		//$rows->bindValue(':insip', $insip, PDO::PARAM_STR);
		$rows->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$this->pdo_exec($rows,__FUNCTION__);
	}
	
#######################################Регистрация пользователей#######################################
###############################################################################################################
	function is_registered($login)
	//Проверяет, есть ли уже такой логин
	{
		$count_rows=$this->db->prepare("SELECT COUNT(user_id) FROM ".$this->param['TABLE_USERS']." WHERE user_login=:login");
		$count_rows->bindValue(':login', $login, PDO::PARAM_STR);
		$this->pdo_exec($count_rows,__FUNCTION__);
		
		//Подсчитаем кол-во записей
		$count_rows=$count_rows->fetch(PDO::FETCH_ASSOC);
		foreach($count_rows as $key=>$value)
			$count_rows=$value;
		//Поместим содержимое в массив
		if ($count_rows=0)
			return false;
		else
			return true;
	}
###############################################################################################################
	function register($login,$password)
	//Регистрирует пользователя
	{
		$rows=$this->db->prepare("INSERT INTO users SET user_login=:login, user_password=:password");
		$rows->bindValue(':login',$login, PDO::PARAM_STR);
		$rows->bindValue(':password',md5(md5(trim($password))), PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);
	}
	
#######################################Назначение прав на запись###############################################
###############################################################################################################
	function set_roots($login,$id)
	//Подключаемся к roots, добавляем запись: id записи, обладатель
	{
		$rows = $this->db->prepare("INSERT INTO `".$this->param['TABLE_ROOTS']."`
							(`id`,`user`)
							VALUES (:id,:login)
				");
		$rows->bindValue(':id', $id, PDO::PARAM_INT);
		$rows->bindValue(':login', $login, PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);
	}
	
##########################################Работа с шаблонами###################################################
###############################################################################################################	
	function get_template($temp,$lang)
	//Отправляет текстовое значение вместо шаблона, в зависимости от языка
	{
		$rows = $this->db->prepare("SELECT :lang FROM ".$this->param['TABLE_TEMPLATES']." WHERE temp=:temp");
		$rows->bindValue(':lang', $lang, PDO::PARAM_STR);
		$rows->bindValue(':temp', $temp, PDO::PARAM_STR);
		$this->pdo_exec($rows,__FUNCTION__);

		if ($template=$rows->fetchAll(PDO::FETCH_ASSOC))
			$template=$template[0];//приводим в нормальный вид
		else
			$template=NULL;
		return $template;
		//SELECT :rus FROM templates WHERE template=:template 
	}
}
?>