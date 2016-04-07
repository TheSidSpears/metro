<?php
$assoc=array(
	"Station Name"=>"Название станции",
	"Line"=>"Цвет линии",
	"Type"=>"Тип",
	"Passengers"=>"Среднее кол-во пассажиров",
	"Money"=>"Средний доход в день",
	"orange"=>"оранжевая",
	"red"=>"красная",
	"blue"=>"синяя",
	"green"=>"зеленая",
	"yellow"=>"желтая",
	"violet"=>"фиолетовая",
	"grey"=>"серая",
	"one"=>"пилонная",
	"two"=>"колонная",
	"three"=>"закрытого типа",
	"four"=>"односводчатая",
	"five"=>"двухярусная",
	"six"=>"однопролетная"
	);



/*устанавливаем соединение, выбираем базу и получаем список и число полей в таблице metro_list
*/
$conn=Mysqli_connect("localhost","root","");
mysqli_set_charset($conn,'utf8');
$database = "metro";
$table_name = "metro_list";
Mysqli_select_db($conn,$database);


//$list_f = Mysqli_list_fields($database,$table_name);//get_columns


$query = "SELECT * FROM $table_name";
if ($result = mysqli_query($conn, $query)) {
	$n1 = Mysqli_num_fields($result);
	// сохраним имена полей в массиве $names
	for($j=0;$j<$n1; $j++)
	{
		$names[] = mysqli_fetch_field_direct($result,$j)->name;
	}	
}
$sql = "SELECT * FROM $table_name"; // создаем SQL запрос
$q = Mysqli_query($conn,$sql) or die(); // отправляем
// запрос на сервер
$n = Mysqli_num_rows($q); // получаем число строк результата

//рисуем HTML-таблицу
echo "<font><b>Список станций</b></font>";
echo "<table>";
// отображаем названия полей
echo "<tr>";
foreach ($names as $val)
{
	if (array_key_exists($val,$assoc))
		echo "<th><font>$assoc[$val]</font></th>";
	else
		echo "<th><font>$val</font></th>";
}
// отображаем значения полей
for($i=0;$i<$n; $i++)
{
// получаем значение поля в виде ассоциативного массива	
    while($row = Mysqli_fetch_array($q, MYSQLI_ASSOC))
    {
        echo "<tr>";
        foreach ($names as $k => $val)
        {
			
            // выводим значение поля
			if (array_key_exists($row[$val],$assoc))
			{
				$val=$row[$val];
				echo "<td><font>&nbsp;$assoc[$val]</font></td>";
			}
			else
				echo "<td><font>&nbsp;$row[$val]</font></td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
?>