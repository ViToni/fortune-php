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
namespace vitoni;

use vitoni\Fortunes\Util\Reader;
use vitoni\Fortunes\Util\FortuneMap;

/**
 * The class scans a fortune file to map the regions where fortunes are found.
 * One can iterate over the fortunes found or retrieve a random fortune.
 */
class Fortunes implements
    \ArrayAccess,
    \Countable,
    \Iterator
{
    /**
     * Used in fortune files as delimiter between fortunes. It acts only as a
     * delimiter, if a line starts with this symbol.
     * As lines starting with delimiter are ignored the delimiter can also be
     * used to add comments to fortune files.
     */
    private const DELIM = '%';

    private readonly string $_filename;

    private readonly array $_mappedFortunes;

    private $_offset = 0;

    public function __construct(string $filename)
    {
        $this->_setFile($filename);
    }

    private function _setFile(string $filename): void
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
            $this->_mappedFortunes = self::_mapFortunes($file);
            $this->_filename = $filename;

            $this->rewind();
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
    private static function _mapFortunes($file): array
    {
        $mappedFortunes = array();

        $fileOffset = 0;
        $fortuneLength = 0;
        $fortune = '';
        $isFortune = false;

        while ($line = fgets($file)) {
            // skip file header and all delimiters
            if (0 === strpos($line, self::DELIM)) {
                // if read a fortune before save its position
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
    public static function addFortuneMap(array &$mappedFortunes, string $fortune, int $offset, int $length): void
    {
        // add only fortune with content - discard fortunes consisting
        // only out of characters regarded as whitespaces
        if (0 < strlen(trim($fortune))) {
            $mappedFortune = new FortuneMap($offset, $length);

            array_push($mappedFortunes, $mappedFortune);
        }
    }

    public function count(): int
    {
        return count($this->_mappedFortunes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->_mappedFortunes[$offset]);
    }

    public function offsetGet(mixed $offset): null|string
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        $mappedFortune = $this->_mappedFortunes[$offset];

        $fortune = Reader::read(
            $this->_filename,
            $mappedFortune->offset,
            $mappedFortune->length
        );

        // remove trailing newline (if any)
        // the last fortune of a file might miss a trailing newline
        return rtrim($fortune, "\n");
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \Exception('Unsupported operation: "offsetSet"');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception('Unsupported operation: "offsetUnset"');
    }

    public function rewind(): void
    {
        $this->_offset = 0;
    }

    public function next(): void
    {
        $this->_offset++;
    }

    public function valid(): bool
    {
        return ($this->_offset < $this->count());
    }

    public function key(): int
    {
        return $this->_offset;
    }

    /**
     * Returns the fortune from the fortune file at the current offset.
     *
     * @return string The fortune at the current (internal) offset.
     */
    public function current(): string
    {
        return $this->offsetGet($this->_offset);
    }

    /**
     * Returns a random fortune from the fortune file.
     *
     * @return string A random fortune
     */
    public function getRandom(): string
    {
        $randOffset = $this->getRandomOffset();

        return $this->offsetGet($randOffset);
    }

    /**
     * Returns a random offset for a fortune from the fortune file.
     *
     * @return int A random offset for a fortune
     */
    public function getRandomOffset(): int
    {
        $maxOffset = $this->count() - 1;

        return random_int(0, $maxOffset);
    }

}
