<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use vitoni\Fortunes;

$filename = __DIR__ . '/../public/datfiles/murphy';

$fortunes = Fortunes::from($filename);

for ($i = 0; $i < 10; $i++) {
    echo "-----\n";
    echo $fortunes->getRandom();
    echo "\n";
}
echo "-----\n";
