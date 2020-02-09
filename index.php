<?php

include_once 'library.php';
//Вфайле index.php и проходит тестирование библиотеки (задание 3)
$file = "test.txt";
$str = "радость";

$cmp = new SubstrSearch();
$result = $cmp -> search($file, $str);

//print_r($result);
?>

<table>
Поиск <kbd><?= htmlentities($str) ?></kbd>
в файле <kbd><?= htmlentities($file) ?></kbd>
		<tr>
			<tr> <td>Номера строк в файле в которых была найдена искомая строка: 
			<?php foreach ($result as &$key) {
			 $a=$key['index']+1;
			 echo "$a ";}?></td></tr>
			<tr><td>Строки:<br> <?php foreach ($result as &$key) {$b=$key['str'];
			  echo "$b <br>";}?></td></tr>
			<tr><td>Позиции которые занимает искомая подстрока в строках в файле: <?php foreach ($result as &$key) {
			  $c = $key['strpos']+1;
			  echo "$c ";}?></td></tr>