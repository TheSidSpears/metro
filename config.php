<?php
######Параметры для подключения к БД######
$db_parameters=array(
    'DB_DRIVER'=>'mysql',
    'DB_HOST'=>'localhost',
    'DB_NAME'=>'metro',
    'DB_USER'=>'root',
    'DB_PASS'=>'',
    'TABLE_NAME'=>'metro_list', //таблица основная
	'TABLE_USERS'=>'users',
	'TABLE_ROOTS'=>'roots', //таблица с правами
	'TABLE_TEMPLATES'=>'templates'
);

######Расположение CSS файла######
$css_file="css/main.css";
//$css_file="css/s.css";
//$css_file="";
######Содержимое HTML######
$parse = new template;

$parse->set_tpl('{A_TITLE}','Добавление новой станции');
$parse->set_tpl('{C_TITLE}','Изменение станции');
$parse->set_tpl('{D_TITLE}','Удаление станции');
$parse->set_tpl('{STATION_NAME}','Название станции');
$parse->set_tpl('{OLD_STATION_NAME}','Текущее название станции');
$parse->set_tpl('{NEW_STATION_NAME}','Новое название станции');
$parse->set_tpl('{LINE}','Линия');
$parse->set_tpl('{LINE_VALUE_1}','Оранжевая');
$parse->set_tpl('{LINE_VALUE_2}','Красная');
$parse->set_tpl('{LINE_VALUE_3}','Голубая');
$parse->set_tpl('{LINE_VALUE_4}','Зеленая');
$parse->set_tpl('{LINE_VALUE_5}','Желтая');
$parse->set_tpl('{LINE_VALUE_6}','Фиолетовая');
$parse->set_tpl('{LINE_VALUE_7}','Серая');
$parse->set_tpl('{TYPE}','Тип');
$parse->set_tpl('{TYPE_VALUE_1}','Пилонная');
$parse->set_tpl('{TYPE_VALUE_2}','Колонная');
$parse->set_tpl('{TYPE_VALUE_3}','Закрытого типа');
$parse->set_tpl('{TYPE_VALUE_4}','Односводчатая');
$parse->set_tpl('{TYPE_VALUE_5}','Двухярусная');
$parse->set_tpl('{TYPE_VALUE_6}','Однопролетная');
$parse->set_tpl('{PASSENGERS}','Среднее кол-во пассажиров');
$parse->set_tpl('{MONEY}','Средний доход в день');
$parse->set_tpl('{A_BUTTON}','Добавить станцию');
$parse->set_tpl('{C_BUTTON}','Изменить станцию');
$parse->set_tpl('{D_BUTTON}','Удалить станцию');
?>