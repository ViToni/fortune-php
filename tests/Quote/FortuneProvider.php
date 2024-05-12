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

final class FortuneProvider
{
    public static function fortuneProvider(): array
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
