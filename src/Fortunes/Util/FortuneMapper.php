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

/**
 * The class scans fortune files to map the regions where fortunes are found.
 * These maps can can be used later on to retrieve the fortunes from the
 * processed file.
 */
class FortuneMapper
{
    /**
     * Used in fortune files as delimiter between fortunes. It acts only as a
     * delimiter, if a line starts with this symbol.
     * As lines starting with delimiter are ignored the delimiter can also be
     * used to add comments to fortune files.
     */
    private const DELIM = '%';

    /**
     * Returns an array containing the found fortunes.
     * Depending on the file the array can be empty.
     *
     * @param $filename Name of file to be mapped 
     *
     * @return array Array of FortuneMap
     */
    public static function mapFile(string $filename): array
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('$filename MUST NOT be empty');
        }
        if (!is_file($filename)) {
            throw new \InvalidArgumentException('$filename MUST be actual file: ' . $filename);
        }
        if (!is_readable($filename)) {
            throw new \InvalidArgumentException('$filename MUST be readable: ' . $filename);
        }

        $file = fopen($filename, 'r');
        try {
            return self::_mapFile($file);
        } finally {
            fclose($file);
        }
    }

    /**
     * Returns an array containing the found fortunes.
     * Depending on the file the array can be empty.
     *
     * @param $file Fortune file to be mapped
     *
     * @return array Array of FortuneMap
     */
    private static function _mapFile($file): array
    {
        $mappedFortunes = array();

        $fileOffset = 0;
        $fortuneLength = 0;
        $fortune = '';
        $isFortune = false;

        while ($line = fgets($file)) {
            // skip file header and all delimiters
            if (0 === strpos($line, self::DELIM)) {
                // if read a fortune before, save its position
                if ($isFortune) {
                    self::addFortuneMap($mappedFortunes, $fortune, $fileOffset, $fortuneLength);

                    $isFortune = false;

                    // adjust position by length of the fortune found
                    $fileOffset += $fortuneLength;
                    // reset to be prepared for next fortune
                    $fortuneLength = 0;
                    $fortune = '';
                }
                // next fortune can only start _after_ the delimited line
                $fileOffset += strlen($line);
            } else {
                if (!$isFortune) {
                    $isFortune = true;
                }
                // increase length of fortune by line found
                $fortuneLength += strlen($line);
                $fortune = $fortune . $line;
            }
        }

        // there might be no delimiter as trigger after the last fortune in a file
        if ($isFortune) {
            self::addFortuneMap($mappedFortunes, $fortune, $fileOffset, $fortuneLength);
        }

        return $mappedFortunes;
    }

    /**
     * Adds the given coordinates as a FortuneMap to the array if the size of
     * the trimmed fortune is greater than zero. Trimming avoids adding empty
     * lines as fortunes.
     */
    public static function addFortuneMap(array &$mappedFortunes, string $fortune, int $offset, int $length)
    {
        // add only fortune with content - discard fortunes consisting
        // only out of characters regarded as whitespaces
        if (0 < strlen(trim($fortune))) {
            $mappedFortune = new FortuneMap($offset, $length);

            array_push($mappedFortunes, $mappedFortune);
        }
    }

}
