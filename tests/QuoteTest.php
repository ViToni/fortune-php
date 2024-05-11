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

use PHPUnit\Framework\TestCase;

final class QuoteTest extends TestCase
{
    const QUOTE = "To be, or not to be, that is the question.";
    const SOURCE = "William Shakespeare";
    const CITE = "Hamlet (Act 3, Scene 1)";

    public function testEmptySourceEmptyCite(): void
    {
        $quote = new Quote(
            self::QUOTE,
            '',
            ''
        );

        $this->assertEquals(self::QUOTE, $quote->getQuote());
        $this->assertFalse($quote->hasSource());
        $this->assertEmpty($quote->getSource());
        $this->assertFalse($quote->hasCite());
        $this->assertEmpty($quote->getCite());
    }

    public function testEmptySource(): void
    {
        $quote = new Quote(
            self::QUOTE,
            self::SOURCE,
            ''
        );

        $this->assertEquals(self::QUOTE, $quote->getQuote());
        $this->assertTrue($quote->hasSource());
        $this->assertEquals(self::SOURCE, $quote->getSource());
        $this->assertFalse($quote->hasCite());
        $this->assertEmpty($quote->getCite());
    }

    public function testQuote(): void
    {
        $quote = new Quote(
            self::QUOTE,
            self::SOURCE,
            self::CITE
        );

        $this->assertEquals(self::QUOTE, $quote->getQuote());
        $this->assertTrue($quote->hasSource());
        $this->assertEquals(self::SOURCE, $quote->getSource());
        $this->assertTrue($quote->hasCite());
        $this->assertEquals(self::CITE, $quote->getCite());
    }

}
