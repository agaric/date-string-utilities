<?php

declare(strict_types=1);

namespace acurrieclark\DateStringUtilities;

use function array_search;
use function implode;
use function preg_match;
use function strtolower;

final class DateInStringFinder
{
    /**
     * @var string[]
     */
    private const MONTHS = [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
    ];

    /**
     * @var string[]
     */
    private const SHORT_MONTHS = [
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
    ];

    /**
     * @var string[]
     */
    private const DAYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    /**
     * @var string[]
     */
    private const SHORT_DAYS = [
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
    ];

    /**
     * @var string[]
     */
    private const ORDINALS = [
        'st', 'nd', 'rd', 'th',
    ];

    /**
     * @return int[]
     */
    public static function find(string $string): array
    {
        $day = null;
        $month = null;
        $year = null;

        [$day, $month, $year] = self::getSimpleDate($string) ?? self::getComplexDate($string);

        // Match month name:
        if ($month === null) {
            $month = self::getMonth($string);
        }

        // Match 5th 1st day:
        if ($day === null) {
            preg_match('/(\d?\d)('.implode('|', self::ORDINALS).')/', $string, $matches_day);
            if ($matches_day && $matches_day[1]) {
                $day = $matches_day[1];
            }
        }

        // Match Year if not already set:
        if ($year === null) {
            preg_match('/\d{4}/', $string, $matches_year);
            if ($matches_year && $matches_year[0]) {
                $year = $matches_year[0];
            }
        }
        if ($year === null) {
            preg_match('/\'(\d{2})/', $string, $matches_year);
            if ($matches_year && $matches_year[1]) {
                $year = $matches_year[1];
            }
        }

        return array_map(static function ($value) {
            return $value === null ? null : (int) $value;
        }, [
            'day' => $day,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * @return int[]|null
     */
    private static function getSimpleDate(string $string): ?array
    {
        // Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
        preg_match('/(\d?\d)[.\-\/ ]+([0-1]?\d)[.\-\/ ]+(\d{2,4})/', $string, $matches);
        if ($matches[1] !== null && $matches[2] !== null && $matches[3] !== null) {
            return [
                $matches[1],
                $matches[2],
                $matches[3],
            ];
        }

        return null;
    }

    private static function getComplexDate(string $string): ?array
    {
        // Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015
        preg_match('/(?:(?:'.implode('|', self::DAYS).'|'.implode('|', self::SHORT_DAYS).')[ ,\-_\/]*)?(\d?\d)[ ,\-_\/]*(?:'.implode('|', self::ORDINALS).')?[ ,\-_\/(?:of)]*('.implode('|', self::MONTHS).'|'.implode('|', self::SHORT_MONTHS).')(?:[ ,\-_\/]+(?:(\d{4})|\'(\d{2})))?/i', $string, $matches);
        [$day, $month, $year] = array_slice($matches, 1, 3);
        $year = ($year) ?: $matches[4];
        if ($month !== null) {
            $month = self::getMonthNumber($month);
        }

        // Match dates: March 1st 2015; March 1 2015; March-1st-2015
        preg_match('/('.implode('|', self::MONTHS).'|'.implode('|', self::SHORT_MONTHS).')[ ,\-_\/]*(\d?\d)[ ,\-_\/]*(?:'.implode('|', self::ORDINALS).')?[ ,\-_\/]+(?:(\d{4})|\'(\d{2}))/i', $string, $matches);
        if ($matches) {
            if ($month === null && $matches[1]) {
                $month = self::getMonthNumber($matches[1]);
            }

            $day = $day ?? $matches[2];

            $year = ($year ?? $matches[3]) ?: $matches[4];
        }

        return [
            $day,
            $month,
            $year,
        ];
    }

    private static function getMonthNumber(string $initialMonth): ?int
    {
        $month = array_search(strtolower($initialMonth), self::SHORT_MONTHS, true);

        if ($month === false) {
            $month = (int) array_search(strtolower($initialMonth), self::MONTHS, true);
        }

        return $month !== false ? ++$month : null;
    }

    private static function getMonth(string $string): ?int
    {
        $month = null;
        preg_match('/('.implode('|', self::MONTHS).')\b/i', $string, $matches_month_word);
        if ($matches_month_word && $matches_month_word[1]) {
            $month = array_search(strtolower($matches_month_word[1]), self::MONTHS);
        }
        // Match short month names
        if ($month === null) {
            preg_match('/('.implode('|', self::SHORT_MONTHS).')\b/i', $string, $matches_month_word);
            if ($matches_month_word && $matches_month_word[1]) {
                $month = array_search(strtolower($matches_month_word[1]), self::SHORT_MONTHS);
            }
        }

        return $month !== null ? ++$month : null;
    }
}
