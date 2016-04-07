
        <form name="add_query" method="post" action="index.php?p=a" accept-charset="utf-8">
            <font>{STATION_NAME}</font><br>
            <input class="colortext" name="station_name" size=20>
            <br><font>{LINE}</font><br>
            <select name="line" size="1">
                <option value="orange" selected >{LINE_VALUE_1}</option>
                <option value="red">{LINE_VALUE_2}</option>
                <option value="blue">{LINE_VALUE_3}</option>
                <option value="green">{LINE_VALUE_4}</option>
                <option value="yellow">{LINE_VALUE_5}</option>
                <option value="violet">{LINE_VALUE_6}</option>
                <option value="grey">{LINE_VALUE_7}</option>
            </select>
            <br><font>{TYPE}</font><br>
            <select name="type" size="1">
                <option value="one" selected>{TYPE_VALUE_1}</option>
                <option value="two">{TYPE_VALUE_2}</option>
                <option value="three">{TYPE_VALUE_3}</option>
                <option value="four">{TYPE_VALUE_4}</option>
                <option value="five">{TYPE_VALUE_5}</option>
                <option value="six">{TYPE_VALUE_6}</option>
            </select>
            <br><font>{PASSENGERS}</font><br>
            <input name="avg_passengers" size=20 onkeyup="return only_num(this);" onchange="return only_num(this);" >
            <br><font>{MONEY}</font><br>
            <input name="avg_money" size=20 onkeyup="return only_num(this);" onchange="return only_num(this);" ><br>
            <input type=submit name="submit" value="{A_BUTTON}">
        </form>