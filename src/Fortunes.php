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
 * The Fortunes class allows to access individual fortunes or get a random
 * fortune out of multiple fortune files.
 *
 * This implementation uses a pre-processed data structure which contains the
 * maps of all fortunes found. Pre-processed maps can be used as an optimization
 * to avoid scanning a given fortune file / directory on each instantiation if
 * there no changes.
 */
class Fortunes implements
    \ArrayAccess,
    \Countable,
    \Iterator
{
    /**
     * Total number of fortunes found.
     */
    private readonly int $_totalCount;

    /**
     * All fortunes found mapped to their files and region.
     */
    private readonly array $_mappedFortuneFiles;

    private $_offset = 0;

    public function __construct(array $mappedFortuneFiles)
    {
        $totalCount = 0;
        foreach ($mappedFortuneFiles as $mappedFortunes) {
            $totalCount += count($mappedFortunes);
        }

        $this->_totalCount = $totalCount;
        $this->_mappedFortuneFiles = $mappedFortuneFiles;

        $this->rewind();
    }

    public function count(): int
    {
        return $this->_totalCount;
    }

    public function offsetExists(mixed $offset): bool
    {
        if (gettype($offset) !== 'integer') {
            return false;
        }

        return $offset < $this->count();
    }

    public function offsetGet(mixed $offset): null|string
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        // to find the given offset which spans all files, $start and $end are
        // used as a search window to find the actual file. the search window
        // is only as wide as number of fortunes in the inspected file
        $start = 0;
        $end = 0;
        foreach ($this->_mappedFortuneFiles as $filepath => $mappedFortunes) {
            // increase upper bound by number of fortunes in current file
            // to move search window
            $end += count($mappedFortunes);

            // need only to check upper bound as $offset is positive and
            // search starts at zero. maybe $offset is now in range
            if ($offset < $end) {
                // map $offset to offset in array of mapped fortunes of file
                $offsetInMappedFortunes = $offset - $start;
                $mappedFortune = $mappedFortunes[$offsetInMappedFortunes];

                $fortune = Reader::read(
                    $filepath,
                    $mappedFortune->offset,
                    $mappedFortune->length
                );

                // remove trailing newline (if any)
                // the last fortune of a file might miss a trailing newline
                return rtrim($fortune, "\n");
            }

            // move lower bound of search window. $start needs to be updated
            // to compute the actual offset within the matching file
            $start = $end;
        }

        // should never get here
        return null;
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
     * Returns the fortune from the fortune file(s) at the current offset.
     *
     * @return string The fortune at the current (internal) offset.
     */
    public function current(): string
    {
        return $this->offsetGet($this->_offset);
    }

    /**
     * Returns a random fortune from the mapped fortune file(s).
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

    /**
     * Scans a fortune file / directory for fortunes and returns a configured
     * Fortunes instance.
     * 
     * The method uses FortuneMapper and as such is not optimized as it scans
     * the given file / directory each time when called.
     */
    public static function from(string $path): Fortunes
    {
        $mappedFortuneFiles = FortuneMapper::map($path);

        return new Fortunes($mappedFortuneFiles);
    }

}
