<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use vitoni\Fortunes;

$filename = __DIR__ . '/datfiles/murphy';

$fortunes = new Fortunes($filename);

$count = min(10, $fortunes->count());
for ($i = 0; $i < $count; $i++) {
    echo "-----\n";
    echo $fortunes[$i];
    echo "\n";
}
echo "-----\n";
