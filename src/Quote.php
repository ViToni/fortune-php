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

/**
 * Representation of a fortune cookie as structured data.
 * This class is especially useful when a fortune is an actual quote and should
 * be displayed accordingly.
 */
class Quote
{
    private readonly string $quote;
    private readonly string $source;
    private readonly string $cite;

    public function __construct(string $quote, string $source = '', string $cite = '')
    {
        $this->quote = $quote;
        $this->source = $source;
        $this->cite = $cite;
    }

    /**
     * Returns the actual quote.
     * 
     * @return string
     */
    public function getQuote(): string
    {
        return $this->quote;
    }

    /**
     * Returns whether the quote has a source.
     * 
     * @return bool
     */
    public function hasSource(): bool
    {
        return !empty($this->source);
    }

    /**
     * Returns source of the quote (if any).
     * 
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Returns whether the quote has a cite.
     * 
     * @return bool
     */
    public function hasCite(): bool
    {
        return !empty($this->cite);
    }

    /**
     * Returns the cite of the quote.
     * 
     * @return string
     */
    public function getCite(): string
    {
        return $this->cite;
    }

}
