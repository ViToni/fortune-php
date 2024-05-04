#!/usr/bin/php
<?php declare(strict_types=1);
/*
 * This file is part of vitoni/fortune.
 *
 * (c) Victor Toni <victor.toni@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * SPDX-License-Identifier: BSD-3-Clause
 */
require_once __DIR__ . '/../vendor/autoload.php';

use vitoni\Fortunes\Util\FortuneMapper;

function usage($name = __FILE__, $message = null)
{
    $name = basename($name);

    echo "Usage: php " . $name . " path\n";
    echo "\n";
    echo "  path - fortune file or directory of fortune files\n";

    if (!empty($message)) {
        echo $message;
        exit(1);
    }
}

function main($argv)
{
    $name = $argv[0];

    if (!array_key_exists(1, $argv)) {
        usage($name, "\nERROR: no path given\n");
    }

    $path = $argv[1];

    if (empty($path)) {
        usage($name, "\nERROR: path was empty\n");
    }

    if (!is_file($path) && !is_dir($path)) {
        usage($name, "\nERROR: path not a file or directory\n");
    }

    if (!is_readable($path)) {
        usage($name, "\nERROR: path not readable\n");
    }

    echo createFortunesWithIndex($path);
}

/**
 * Returns a PHP script with a configured Fortunes instance.
 * The given path is scanned for fortunes and the result exported
 * and used to set up a Fortunes instance.
 * The resulting script can be included by another script for further use.
 */
function createFortunesWithIndex(string $path): string
{
    $mappedFortuneFiles = FortuneMapper::map($path);
    $mappedFortuneFilesExport = var_export(
        $mappedFortuneFiles,
        true                    // return as value
    );

    $s = "<?php declare(strict_types=1);\n";
    $s = $s . "\n";
    $s = $s . 'require_once __DIR__ . "/vendor/autoload.php";' . "\n";
    $s = $s . "\n";
    $s = $s . 'use vitoni\Fortunes;' . "\n\n";
    $s = $s . "\n";
    $s = $s . "\n";
    $s = $s . '$mappedFortuneFiles = ' . $mappedFortuneFilesExport . ";\n";
    $s = $s . "\n";
    $s = $s . '$fortunes = new Fortunes($mappedFortuneFiles);' . "\n";
    $s = $s . "\n";

    return $s;
}

main($argv);
