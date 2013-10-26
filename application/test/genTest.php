<?php
$bootstrap = "../bootstrap.php";
$libs = "../core";
$tests = "../test";

$params = $_SERVER['argv'];
$class_name = $params[1];

// convert [php5.3 namespace separator] to [path separator].
if (false !== strpos($class_name, '\\')) {
    $cls_str = str_replace('\\', '/', $class_name);

// other, ["_" separator] to [path separator].
} else {
    $cls_str = str_replace('_', '/', $class_name);
}

if (!file_exists("${tests}/${cls_str}Test.php")) {
    $dir = pathinfo("${tests}/${cls_str}Test.php", PATHINFO_DIRNAME);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    touch("${tests}/${cls_str}Test.php");
}

exec("phpunit-skelgen --test --bootstrap ${bootstrap} -- ${class_name} ${libs}/${cls_str}.php ${class_name}Test ${tests}/${cls_str}Test.php", $output, $ret);

foreach ($output as $row) {
    echo $row . PHP_EOL;
}
