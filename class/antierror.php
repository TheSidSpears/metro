<?php
include ("config.php");

//Станция: 3-30 символов. Первая буква, потом буквы, возможно пробел, буквы или цифры


class antierror
{
private $error;
//заменить шаблоны на имена
###############################################################################################################
    function __construct($ud)
    {
        $error=array();
        
        if ($ud['station_name']=='')
		{
            $error[]='Заполните поле "Название станции"';
		}	
		
        if (isset($ud['current_station_name']))
            if ($ud['current_station_name']=='')
                $error[]='Заполните поле "Текущее название станции"';
        
        if (isset($ud['line']))
            if ($ud['line']=='')
                $error[]='Сделайте выбор в "Линия"';

        if (isset($ud['type']))
            if ($ud['type']=='')
                $error[]='Сделайте выбор в "Тип"';

        
        if (isset($ud['avg_passengers']))
        {
            if ($ud['avg_passengers']=='')
                $error[]='Заполните поле "Среднее кол-во пассажиров"';
            
            if ($ud['avg_passengers']==0)
                $error[]='Поле "Среднее кол-во пассажиров" не должно быть = 0';
        }
        
        if (isset($ud['avg_money']))
        {
            if ($ud['avg_money']=='')
                $error[]='Заполните поле "Средний доход в день"';
 
            if ($ud['avg_money']==0)
                $error[]='Поле "Средний доход в день" не должно быть = 0';           
        }
		$this->error=$error;
    }
###############################################################################################################
	function get_error()
    {
        return $this->error;
    }

}
?>