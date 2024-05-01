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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FortunesTest extends TestCase
{
    const TEST_FILES_DIR = __DIR__ . '/_files';

    #[DataProvider('fileWithoutFortunesProvider')]
    public function testFileWithoutFortunes(string $filename): void
    {
        $filename = self::TEST_FILES_DIR . '/' . $filename;

        $fortunes = new Fortunes($filename);

        $this->assertEquals(0, $fortunes->count());
        $this->assertEquals(0, $fortunes->key());
        $this->assertFalse($fortunes->offsetExists(0));
    }

    #[DataProvider('fortuneFileProvider')]
    public function testFortuneFile(string $filename, string $fortunePrefix): void
    {
        $filename = self::TEST_FILES_DIR . '/' . $filename;

        $fortunes = new Fortunes($filename);

        $this->assertEquals(4, $fortunes->count());

        // array access

        $this->assertFalse($fortunes->offsetExists(4));
        $this->assertFalse($fortunes->offsetExists($fortunes->count()));

        for ($i = 0; $i < $fortunes->count(); $i++) {
            $value = $fortunes[$i];
            $expected = $fortunePrefix . ($i + 1);

            $this->assertEquals($expected, $value);
        }

        // iterator

        $this->assertEquals(0, $fortunes->key());

        foreach ($fortunes as $key => $value) {
            $expected = $fortunePrefix . ($key + 1);

            $this->assertEquals($expected, $value);

            $this->assertTrue($fortunes->offsetExists($key));
        }
    }

    // this test might be flaky once every billion times
    // but ensures that no fixed offset has been used by mistake
    public function testRandomness(): void
    {
        $filename = self::TEST_FILES_DIR . '/10_singleline_fortunes';

        $fortunes = new Fortunes($filename);

        $values = array();
        for ($i = 0; $i < 32; $i++) {
            $offset = $fortunes->getRandomOffset();

            $values[$offset] = 1;
        }

        $this->assertGreaterThan(1, count($values));
    }

    public function testAddingFortunes(): void
    {
        $mappedFortunes = array();

        $fortune = "All warranties expire upon payment of invoice.\n";
        $offset = 0;
        $length = strlen($fortune);

        $this->assertEquals(0, count($mappedFortunes));
        Fortunes::addFortuneMap($mappedFortunes, $fortune, $offset, $length);
        $this->assertEquals(1, count($mappedFortunes));
    }

    public function testSkippAddingEmptyFortunes(): void
    {
        $mappedFortunes = array();

        $fortune = "  \n\t\t\n";
        $offset = 0;
        $length = strlen($fortune);

        $this->assertEquals(0, count($mappedFortunes));
        Fortunes::addFortuneMap($mappedFortunes, $fortune, $offset, $length);
        $this->assertEquals(0, count($mappedFortunes));
    }

    // helper methods

    public static function fileWithoutFortunesProvider()
    {
        // filename
        return [
            'empty file' => [
                '00_empty_file'
            ],
            'file containing only delimiter' => [
                '01_only_delimiter'
            ],
            'file containing only empty fortunes' => [
                '02_empty_fortunes'
            ]
        ];
    }

    public static function fortuneFileProvider()
    {
        // filename
        // fortunePrefix
        return [
            'single-line fortunes' => [
                '10_singleline_fortunes',
                'This is fortune '
            ],
            'multi-line fortunes' => [
                '11_multiline_fortunes',
                "This\nis\nmultiline\nfortune "
            ],
            'ignore empty fortunes' => [
                '12_ignore_empty_fortunes',
                'This is fortune '
            ]
        ];
    }

}
