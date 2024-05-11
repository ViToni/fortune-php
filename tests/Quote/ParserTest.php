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

final class ParserTest extends TestCase
{
    const QUOTE = 'To be, or not to be, that is the question.';
    const SOURCE = 'William Shakespeare';
    const CITE = 'Hamlet (Act 3, Scene 1)';

    const PREFIX = '  -- ';

    public function testSimpleFortune(): void
    {
        $fortune =
            ParserTest::QUOTE . "\n";

        $parts = Parser::getParts($fortune);

        $this->assertEquals(ParserTest::QUOTE, $parts['quote']);
        $this->assertEmpty($parts['source']);
        $this->assertEmpty($parts['cite']);
    }

    #[DataProvider('quoteProvider')]
    public function testFortuneWithSource(string $quote, string $source, $ignoredCite): void
    {
        $fortune =
            $quote . "\n" .
            ParserTest::PREFIX . $source . "\n";

        $parts = Parser::getParts($fortune);

        $this->assertEquals($quote, $parts['quote']);

        $this->assertNotEmpty($parts['source']);
        $this->assertEquals($source, $parts['source']);

        $this->assertEmpty($parts['cite']);
    }

    /* Test for quotes in the form of:
    -----------------------------------------------------
        To be, or not to be, that is the question.
          -- William Shakespeare
          -- Hamlet (Act 3, Scene 1)
    -----------------------------------------------------
     */
    #[DataProvider('quoteProvider')]
    public function testFortuneWithSourceAndDelimitedCite(string $quote, string $source, $cite): void
    {
        $fortune =
            $quote . "\n" .
            ParserTest::PREFIX . $source . "\n" .
            ParserTest::PREFIX . $cite . "\n";

        $parts = Parser::getParts($fortune);

        $this->assertEquals($quote, $parts['quote']);

        $this->assertNotEmpty($parts['source']);
        $this->assertEquals($source, $parts['source']);

        $this->assertNotEmpty($parts['cite']);
        $this->assertEquals($cite, $parts['cite']);
    }

    /* Test for quotes in the form of:
    -----------------------------------------------------
        To be, or not to be, that is the question.
          -- William Shakespeare (Hamlet (Act 3, Scene 1))
    -----------------------------------------------------
     */
    #[DataProvider('quoteProvider')]
    public function testFortuneWithSourceAndCiteInParantheses(string $quote, string $source, string $cite): void
    {
        $fortune =
            $quote . "\n" .
            ParserTest::PREFIX . $source . ' (' . $cite . ")\n";

        $parts = Parser::getParts($fortune);

        $this->assertEquals($quote, $parts['quote']);

        $this->assertNotEmpty($parts['source']);
        $this->assertEquals($source, $parts['source']);

        $this->assertNotEmpty($parts['cite']);
        $this->assertEquals($cite, $parts['cite']);
    }

    /* Test for quotes in the form of:
    -----------------------------------------------------
        To be, or not to be, that is the question.
          -- William Shakespeare in Hamlet (Act 3, Scene 1)
    -----------------------------------------------------
     */
    #[DataProvider('quoteProvider')]
    public function testFortuneWithSourceAndTextualCite(string $quote, string $source, string $cite): void
    {
        $fortune =
            $quote . "\n" .
            ParserTest::PREFIX . $source . ' in ' . $cite . "\n";

        $parts = Parser::getParts($fortune);

        $this->assertEquals($quote, $parts['quote']);

        $this->assertNotEmpty($parts['source']);
        $this->assertEquals($source, $parts['source']);

        $this->assertNotEmpty($parts['cite']);
        $this->assertEquals($cite, $parts['cite']);
    }

    /* Test for quotes in the form of:
    -----------------------------------------------------
        To be, or not to be, that is the question.
          -- William Shakespeare in Hamlet (Act 3, Scene 1)
    -----------------------------------------------------
     */
    #[DataProvider('fortuneProvider')]
    public function testCommonlyFoundFortunes(string $fortune, string $quote, string $source, string $cite): void
    {
        $parts = Parser::getParts($fortune);

        $this->assertEquals($quote, $parts['quote']);

        $this->assertEquals(empty($source), empty($parts['source']));
        if (!empty($source)) {
            $this->assertEquals($source, $parts['source']);
        }

        $this->assertEquals(empty($cite), empty($parts['cite']));
        if (!empty($cite)) {
            $this->assertEquals($cite, $parts['cite']);
        }
    }

    public static function quoteProvider()
    {
        // quote
        // source
        // cite
        return [
            [
                'To be, or not to be, that is the question.',
                'William Shakespeare',
                'Hamlet (Act 3, Scene 1)'
            ]
        ];
    }

    public static function fortuneProvider()
    {
        // fortune
        // quote
        // source
        // cite
        return [
            [
                "To be, or not to be, that is the question.\n" .
                '  -- William Shakespeare in Hamlet (Act 3, Scene 1)',

                'To be, or not to be, that is the question.',
                'William Shakespeare',
                'Hamlet (Act 3, Scene 1)'
            ],
            [
                "Debugging is twice as hard as writing the code in the first place.\n" .
                "Therefore, if you write the code as cleverly as possible, you are, by definition, not smart enough to debug it.\n" .
                "  -- Brian W. Kernighan and P. J. Plauger in The Elements of Programming Style.",

                "Debugging is twice as hard as writing the code in the first place.\n" .
                "Therefore, if you write the code as cleverly as possible, you are, by definition, not smart enough to debug it.",
                'Brian W. Kernighan and P. J. Plauger',
                'The Elements of Programming Style.'
            ],
            [
                "The crew will not benefit from the leadership of an exhausted caption.\n" .
                "\t\t-- Tuvok, VOY 1x01 \"Caretaker\"",

                'The crew will not benefit from the leadership of an exhausted caption.',
                'Tuvok',
                'VOY 1x01 "Caretaker"'
            ],
            [
                "Nothing to see here. Go along!\n    -- Unnamed authority",

                'Nothing to see here. Go along!',
                'Unnamed authority',
                ''
            ],
            [
                'Math is like love; a simple idea, but it can get complicated.',

                'Math is like love; a simple idea, but it can get complicated.',
                '',
                ''
            ]
        ];
    }

}
