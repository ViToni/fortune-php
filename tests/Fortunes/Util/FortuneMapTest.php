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

use PHPUnit\Framework\TestCase;

final class FortuneMapTest extends TestCase
{
    public function testNegativeOffset(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$offset MUST NOT be negative');

        $offset = -1;
        $length = 1;

        $mappedFortune = new FortuneMap($offset, $length);
    }

    public function testNonPositiveLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$length MUST be positive');

        $offset = 0;
        $length = 0;

        $mappedFortune = new FortuneMap($offset, $length);
    }

    public function testAllowedValues(): void
    {
        $offset = 0;
        $length = 1;

        $mappedFortune = new FortuneMap($offset, $length);

        $this->assertSame($offset, $mappedFortune->offset);
        $this->assertEquals($length, $mappedFortune->length);
    }

}
