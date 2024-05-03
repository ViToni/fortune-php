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
use vitoni\Fortunes\Util\FortuneMapper;

/**
 * The class scans a fortune file to map the regions where fortunes are found.
 * One can iterate over the fortunes found or retrieve a random fortune.
 */
class Fortunes implements
    \ArrayAccess,
    \Countable,
    \Iterator
{
    private readonly string $_filename;

    private readonly array $_mappedFortunes;

    private $_offset = 0;

    public function __construct(string $filename)
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

        $this->_mappedFortunes = FortuneMapper::mapFile($filename);
        $this->_filename = $filename;

        $this->rewind();
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
