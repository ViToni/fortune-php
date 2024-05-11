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

use vitoni\Quote;

/**
 * Parse fortunes into structured data.
 */
class Parser
{
    /**
     * Delimiter between quote and source.
     */
    const SOURCE_DELIMITER = '-- ';

    /**
     * List of eventual delimiters used in order which they will be tested for.
     */
    const CITE_DELIMITER_ORDERED = [
        self::SOURCE_DELIMITER,
        'comma' => ', ',
        'textual' => ' in ',
        'parenthesis' => ' (',
    ];

    public static function parse(string $fortune): Quote
    {
        if (!is_string($fortune) || empty($fortune)) {
            throw new \InvalidArgumentException('Fortune MUST be non-empty string');
        }

        $quoteParts = self::getParts($fortune);

        return new Quote(
            $quoteParts['quote'],
            $quoteParts['source'],
            $quoteParts['cite']
        );
    }

    /**
     * Parse fortune into an associative array ('quote', 'source', 'cite').
     *
     * @return array
     */
    public static function getParts(string $fortune): array
    {
        $fortune = trim($fortune);
        $result = array(
            'quote' => '',
            'source' => '',
            'cite' => ''
        );

        $pos = strpos($fortune, self::SOURCE_DELIMITER);

        // no source found
        if ($pos === false) {
            $result['quote'] = trim($fortune);

            return $result;
        }

        $quote = trim(substr($fortune, 0, $pos));
        $result['quote'] = $quote;

        $pos += strlen(self::SOURCE_DELIMITER);
        $source = trim(substr($fortune, $pos));

        $sourceParts = self::getSourceParts($source);

        return array_merge($result, $sourceParts);
    }

    /**
     * Retrieve parts of source as an associative array ('source', 'cite').
     * 
     * @return array
     */
    public static function getSourceParts(string $source): array
    {
        $source = trim($source);
        $result = array(
            'source' => '',
            'cite' => ''
        );

        $delim = '';
        $delimKey = '';
        $pos = PHP_INT_MAX;
        foreach (self::CITE_DELIMITER_ORDERED as $key => $delimTest) {
            $testPos = strpos($source, $delimTest);
            if ($testPos !== false && $testPos < $pos) {
                $pos = $testPos;
                $delimKey = $key;
                $delim = $delimTest;
            }
        }

        // found a matching delimiter
        if (!empty($delim)) {
            $result['source'] = trim(substr($source, 0, $pos));

            $pos += strlen($delim);
            $cite = trim(substr($source, $pos));

            if ($delimKey === 'parenthesis') {
                // remove trailing parenthesis
                $pos = strrpos($cite, ')');
                if ($pos !== false) {
                    $cite = trim(substr($cite, 0, $pos));
                }
            }

            $result['cite'] = $cite;

            return $result;
        }

        $result['source'] = $source;

        return $result;
    }

}
