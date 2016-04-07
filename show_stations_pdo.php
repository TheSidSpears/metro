<?php
//Получаем названия полей и их количество
$get_fields=$metro->get_columns();
$names=$get_fields['columns'];
var_dump($names);
$n1=$get_fields['count'];

//Получаем содержимое таблицы
//$q=$metro->get_table();
$n=2;
//рисуем HTML-таблицу
echo "&nbsp;<TABLE BORDER=0 CELLSPACING=0 width=90%
align=center><tr><TD BGCOLOR='#005533' align=center>
<font color='#FFFFFF'><b>Метро</b></font></td>
</tr></TABLE>";
echo "<table cellspacing=0 cellpadding=1 border=1
width=90% align=center>";
// отображаем названия полей
echo "<tr>";
foreach ($names as $val)
{
    echo "<th ALIGN=CENTER BGCOLOR='#C2E3B6'>
    <font size=2>$val</font></th>";
}
// отображаем значения полей
for($i=0;$i<$n; $i++)
{
// получаем значение поля в виде ассоциативного массива
  //  while($row = $metro->get_table())
    {
        echo "<tr>";
        foreach ($names as $k => $val)
        {
            // выводим значение поля
            echo "<td><font size=2>&nbsp;$row[$val]</font></td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
?>