<?php
print_r(get_loaded_extensions());
echo "You can filter the PDO ones that start with pdo_:";

foreach (get_loaded_extensions() as $extension) {
    if (substr($extension, 0, 4) == 'pdo_') {
        echo $extension . PHP_EOL;
    }
}
phpinfo();

?>