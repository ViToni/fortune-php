<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use vitoni\Fortunes;

$filename = __DIR__ . '/datfiles/murphy';

$fortunes = Fortunes::from($filename);

$steps = (int) ($fortunes->count() / 10);
foreach ($fortunes as $key => $fortune) {
    if ($key % $steps != 0) {
        continue;
    }
    echo "-----\n";
    echo $fortune;
    echo "\n";
}
echo "-----\n";
