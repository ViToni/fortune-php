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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FortuneMapperTest extends TestCase
{
    private const TEST_FILES_DIR = __DIR__ . '/../../_files';

    #[DataProvider('fileWithoutFortunesProvider')]
    public function testFileWithoutFortunes(string $filename): void
    {
        $filename = self::TEST_FILES_DIR . '/' . $filename;

        $mappedFortunes = FortuneMapper::mapFile($filename);

        $this->assertEquals(0, count($mappedFortunes));
    }

    /**
     * This tests covers also the functionality of the Reader.
     */
    #[DataProvider('fortuneFileProvider')]
    public function testFortuneFile(string $filename, string $fortunePrefix): void
    {
        $filename = self::TEST_FILES_DIR . '/' . $filename;

        $mappedFortunes = FortuneMapper::mapFile($filename);

        $this->assertEquals(4, count($mappedFortunes));

        foreach ($mappedFortunes as $key => $mappedFortune) {
            $expected = $fortunePrefix . ($key + 1) . "\n";

            $fortune = Reader::read($filename, $mappedFortune->offset, $mappedFortune->length);

            $this->assertEquals($expected, $fortune);
        }
    }


    public function testAddingFortuneMap(): void
    {
        $mappedFortunes = array();

        $fortune = "All warranties expire upon payment of invoice.\n";
        $offset = 0;
        $length = strlen($fortune);

        $this->assertEquals(0, count($mappedFortunes));
        FortuneMapper::addFortuneMap($mappedFortunes, $fortune, $offset, $length);
        $this->assertEquals(1, count($mappedFortunes));
    }

    public function testSkippAddingFortuneMapWithEmptyFortune(): void
    {
        $mappedFortunes = array();

        $fortune = "  \n\t\t\n";
        $offset = 0;
        $length = strlen($fortune);

        $this->assertEquals(0, count($mappedFortunes));
        FortuneMapper::addFortuneMap($mappedFortunes, $fortune, $offset, $length);
        $this->assertEquals(0, count($mappedFortunes));
    }

    public function testSkipEmptyFortuneFiles(): void
    {
        $mappedFortuneFiles = FortuneMapper::mapDirectory(self::TEST_FILES_DIR);

        // this array might need to be updated when files are added
        $filesWithoutFortunes = array(
            '00_empty_file',
            '01_only_delimiter',
            '02_empty_fortunes'
        );

        // this array needs to be updated when files are added
        $filesWithFortunes = array(
            '10_singleline_fortunes',
            '11_multiline_fortunes',
            '12_ignore_empty_fortunes',
            '13_singleline_n_empty_fortunes-o',
            '14_multiline_n_empty_fortunes-o'
        );

        foreach ($mappedFortuneFiles as $filename => $mappedFortunes) {
            foreach ($filesWithoutFortunes as $fileWithoutFortunes) {
                $this->assertFalse(str_contains($filename, $fileWithoutFortunes));
            }
            $fileWithFortunesFound = false;
            foreach ($filesWithFortunes as $fileWithFortunes) {
                if (str_contains($filename, $fileWithFortunes)) {
                    $fileWithFortunesFound = true;
                    break;
                }
            }
            $this->assertTrue($fileWithFortunesFound, 'File not found: ' . $filename);
        }
    }

    public function testAddMappedFortuneFile(): void
    {
        $filename = self::TEST_FILES_DIR . '/10_singleline_fortunes';

        $mappedFortunefiles = array();
        $this->assertEquals(0, count($mappedFortunefiles));

        FortuneMapper::addMappedFortuneFile($mappedFortunefiles, $filename);
        $this->assertEquals(1, count($mappedFortunefiles));
    }

    public function testAddMappedFortuneFileWithEmptyFortuneFiles(): void
    {
        $filename = self::TEST_FILES_DIR . '/00_empty_file';

        $mappedFortunefiles = array();
        $this->assertEquals(0, count($mappedFortunefiles));

        FortuneMapper::addMappedFortuneFile($mappedFortunefiles, $filename);
        $this->assertEquals(0, count($mappedFortunefiles));
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
            ],
            'encrypted single-file fortunes' => [
                '13_singleline_n_empty_fortunes-o',
                'This is fortune '
            ],
            'encrypted multi-file fortunes' => [
                '14_multiline_n_empty_fortunes-o',
                "This\nis\nmultiline\nfortune "
            ]
        ];
    }

}
