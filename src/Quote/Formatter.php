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
 * Formatter: Makes formatting of a Quote a one-call operation
 */
class Formatter
{
    public static function format(Quote $quote, string $indent = ''): string
    {
        $htmlQuote = $quote->getQuote();
        $htmlQuote = self::encode($htmlQuote);

        $output =
            "$indent<figure class='quote'>\n" .
            "$indent    <blockquote>$htmlQuote</blockquote>\n" .
            self::getCaption($quote, $indent) .
            "$indent</figure>\n";

        return $output;
    }

    public static function getCaption(Quote $quote, string $indent = ''): string
    {
        if ($quote->hasSource()) {
            $source = $quote->getSource();
            $source = self::encode($source);
            $caption = $source . self::getCite($quote);

            return "$indent    <figcaption>$caption</figcaption>\n";
        }

        return '';
    }

    public static function getCite(Quote $quote): string
    {
        if ($quote->hasCite()) {
            $cite = $quote->getCite();
            $cite = self::encode($cite);

            return ", <cite>$cite</cite>";
        }

        return '';
    }

    public static function encode($text): string
    {
        $encoded = htmlentities($text, ENT_QUOTES, 'UTF-8');

        return str_replace("\n", "<br />\n", $encoded);
    }

}
