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
namespace vitoni\Quote;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use vitoni\Quote;

final class FormatterTest extends TestCase
{
    public function testStandIn(): void
    {
        $this->assertTrue(true);
    }

    #[DataProvider('quoteProvider')]
    public function testQuote(Quote $quote): void
    {
        $expected = $quote->getQuote();
        $expected = htmlentities($expected, ENT_QUOTES, 'UTF-8');

        $expected = str_replace("\n", "<br />\n", $expected);

        $actual = Formatter::format($quote);

        $this->assertStringContainsString($expected, $actual);
    }

    #[DataProvider('quoteProvider')]
    public function testSourceFormatting(Quote $quote): void
    {
        if ($quote->hasSource()) {
            $expected = $quote->getSource();

            $expected = htmlentities($expected, ENT_QUOTES, 'UTF-8');
            $expected = str_replace("\n", "<br />\n", $expected);

            $actual = Formatter::getCaption($quote);

            $this->assertStringStartsWith("    <figcaption>", $actual);
            $this->assertStringContainsString($expected, $actual);
            $this->assertStringEndsWith("</figcaption>\n", $actual);
        } else {
            $actual = Formatter::getCaption($quote);
            $this->assertEquals('', $actual);
        }
    }

    #[DataProvider('quoteProvider')]
    public function testCiteFormatting(Quote $quote): void
    {
        if ($quote->hasCite()) {
            $expected = $quote->getCite();

            $expected = htmlentities($expected, ENT_QUOTES, 'UTF-8');
            $expected = str_replace("\n", "<br />\n", $expected);

            $expected = ", <cite>$expected</cite>";

            $actual = Formatter::getCite($quote);

            $this->assertEquals($expected, $actual);
        } else {
            $actual = Formatter::getCite($quote);
            $this->assertEquals('', $actual);
        }
    }

    public static function quoteProvider()
    {
        return array_map('\vitoni\Quote\FormatterTest::quoteFromFortuneData', FortuneProvider::fortuneProvider());
    }

    public static function quoteFromFortuneData(array $fortuneData)
    {
        return array(
            new Quote($fortuneData[1], $fortuneData[2], $fortuneData[3])
        );
    }

}
