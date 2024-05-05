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
namespace vitoni\Fortunes\Util;

class Reader
{
    /**
     * Reads partial data from a file into a string.
     *
     * @param $filename Name of the file to read.
     * @param $offset The offset where the reading starts.
     * @param $length Maximum length of data read.
     * @return bool|string The function returns the read data or `false` on failure.
     */
    public static function read(string $filename, int $offset, int $length): bool|string
    {
        $fortune = file_get_contents(
            $filename,
            FALSE,
            null,
            $offset,
            $length
        );

        if (false !== $fortune) {
            $encrpyted = substr($filename, -2) === '-o';
            if ($encrpyted) {
                $fortune = str_rot13($fortune);
            }
        }

        return $fortune;
    }

}
