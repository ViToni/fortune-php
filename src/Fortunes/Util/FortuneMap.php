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
 * Class holding the postion and length of a fortune within a fortune file.
 */
final class FortuneMap
{
    /**
     * Where does the fortune start within the file.
     */
    public readonly int $offset;

    /**
     * Fortune length.
     */
    public readonly int $length;

    public function __construct(int $offset, int $length)
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException('$offset MUST NOT be negative');
        }
        if ($length < 1) {
            throw new \InvalidArgumentException('$length MUST be positive');
        }

        $this->offset = $offset;
        $this->length = $length;
    }

    public static function __set_state(array $array): FortuneMap
    {
        return new FortuneMap((int) $array["offset"], (int) $array["length"]);
    }

}
