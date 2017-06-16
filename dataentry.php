<?php
/**
 * Created by PhpStorm.
 * User: Michie
 * Date: 6/15/2017
 * Time: 9:10 AM
 */
if ($_GET) {

    if(!empty($_GET['table']) && $_GET['dbname'] ){
        //if the data is from table input(table is not empty)
        $table = $_GET['table'];
        $dbname = $_GET['dbname'];
         $pdo = createpdo($dbname);
        $sql = "desc $table";
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll();
        //terminate script
        echo(json_encode($res));
    die;
    }

        //get the dbname if it is from ajax from dbname input
        $dbname = $_GET['dbname'];
        if(empty($dbname)){
            //prevent instantiating pdo obj when no db
            return false;
        }
        $pdo = createpdo($dbname);
        $sql = 'show tables';
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll();
        //terminate script
        echo(json_encode($res));
        die;
    }
//1.using post to get fields and field type and record quantity
//global parameters-------------------------------
if ($_POST) {
//    echo '<pre/>';var_dump($_POST);die;
    $dbname = $_POST['dbname'] ? $_POST['dbname'] : '';
    $table = $_POST['table'] ? $_POST['table'] : '';
    $fields = $_POST['fields'] ? $_POST['fields'] : '';
    $fieldstr = implode($fields, '`,`');
    $fieldval = ':' . implode($fields, ',:');
    $types = $_POST['types'] ? $_POST['types'] : '';
    $record_count = $_POST['record_count'] ? (int)$_POST['record_count'] : '';
    $number_digit = $_POST['number_digit'] ? (int)$_POST['number_digit'] : '';
    $string_digit = $_POST['string_digit'] ? (int)$_POST['string_digit'] : '';
    $date_format = $_POST['date_format'] ? (int)$_POST['date_format'] : '';
    $pdo = createpdo($dbname);
}
//echo '<pre/>';var_dump($dbname);var_dump($table);var_dump($fields);var_dump($fieldstr);var_dump($fieldval);var_dump($types);var_dump($record_count);var_dump($number_digit);die;
//================================================
//2.make a shuffle int/string/timestamp generator
//create shuffled string
//define type variables, this is for converting type from string to variable and link to shuffled type -----------------------------
$string = 'string';
$number = 'number';
$time = 'time';
$s1 = range('a', 'z');
$s2 = range('A', 'Z');
$s1 = array_merge($s1, $s2);


//create shuffled number/timestamp
$n1 = range('1', '9');

//4. connect to database
//5. use preprocess statement
function createpdo($dbname){

$dbms = 'mysql';
$host = 'localhost';
$port = '3306';
$charset = 'utf8';
$user = 'root';
$pwd = 'y3070288';
$dsn = "$dbms:host=$host;port=$port;dbname=$dbname;charset=$charset";
//create new PDO object, set parameter for __construct
RETURN $pdo = new PDO($dsn, $user, $pwd);
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {

///1.preprocess statement
    $sql = "insert into $table(`$fieldstr`)  values($fieldval)";
    //2.prepare() get PDOstatement object
    $stmt = $pdo->prepare($sql);
    //3a. bind parameter by array, key(positon symbol) => variable
    $condition = "2";
    //traversate fields array to bind parameters with corresonding type
    $count = count($fields);
    foreach ($fields as $k => $v) {
        $stmt->bindParam(":$v", $$types[$k]);
    }
//echo '<pre/>';var_dump($count);die;
    //4.execute $arr, which include respective value and key, return a bool
    $flag = true;
//6. insert record according to quantity (record_count)
    for ($i = 0; $i < $record_count; $i++) {
        //regenerate string and number in each loop
        shuffle($s1);
        $str = join($s1, '');
//extract n digits of string
        $string = substr($str, 0, $string_digit);
        shuffle($n1);
        $num = join($n1, '');
        //extract digits,convert to int
        $number = (int)substr($num, 0, $number_digit);
        $time = date($date_format, $number);

        $res = $stmt->execute();
        $res ? '' : $flag = false;
    }
    //if there is result, echo successful
    if ($flag) {
        echo 'execute successfully<br/>';
    } else {
        echo 'execute failed<br/>';
    }
} catch (PDOException $e) {
    echo $e->getCode();
    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
}
