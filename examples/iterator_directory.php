<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use vitoni\Fortunes;

$path = __DIR__ . '/../tests/_files/';

$fortunes = new Fortunes($path);

foreach ($fortunes as $fortune) {
    echo "-----\n";
    echo $fortune;
    echo "\n";
}
echo "-----\n";
