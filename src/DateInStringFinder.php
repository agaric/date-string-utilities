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

        [$day, $month, $year] = self::getIsoDate($string) ?? self::getSimpleDate($string) ?? self::getComplexDate($string);

        // Match month name:
        if ($month === null) {
            $month = self::getMonth($string);
        }

        // Match 5th 1st day:
        if ($day === null) {
            preg_match(
                '/(\d?\d)('.implode('|', self::ORDINALS).')/',
                $string,
                $matches_day
            );
            if ($matches_day && $matches_day[1]) {
                $day = $matches_day[1];
            }
        }

        // Match YYYY-MM-DD to year, month, and day if not already set.
        if ($year === null && $month === null && $day === null) {
            preg_match('/\d{4}[\-\.\/]\d{2}[\-\.\/]\d{2}/', $string, $matches_year_month_day);
            if ($matches_year_month_day && $matches_year_month_day[0]) {
                $year_month_day = $matches_year_month_day[0];
                $year_month_day = str_replace(['.', '/'], '-', $year_month_day);
                [$year, $month, $day] = explode('-', $year_month_day);
            }
        }

        // Match YYYY-MM to Year and Month if not already set:
        if ($year === null && $month === null) {
            preg_match('/\d{4}[\-\.\/]\d{2}/', $string, $matches_year_month);
            if ($matches_year_month && $matches_year_month[0]) {
                $year_month = $matches_year_month[0];
                $year_month = str_replace(['.', '/'], '-', $year_month);
                [$year, $month] = explode('-', $year_month);
            }
        }

        // Match Year if not already set:
        if ($year === null) {
            preg_match('/\d{4}/', $string, $matches_year);
            if ($matches_year && $matches_year[0]) {
                $year = $matches_year[0];
            }
        }

        // Only if we did not succeed in getting a year do we try to find
        // two-digit years.  And maybe only if no day or month either?
        if ($year === null) {
            [$day, $month, $year] = self::getSimpleDate($string, TRUE) ?? self::getComplexDate($string, TRUE);
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
    private static function getSimpleDate(string $string, bool $two_digit_year = FALSE): ?array
    {
        $year_pattern = '(\d{4})';
        if ($two_digit_year) {
          $year_pattern = '(\d{2})';
        }

        // Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
        preg_match(
            '/(\d?\d)([.\-\/ ])+([0-1]?\d)\2+' . $year_pattern . '/',
            $string,
            $matches
        );
        if (($matches[1] ?? null) !== null && ($matches[3] ?? null) !== null && ($matches[4] ?? null) !== null) {
            return [
                $matches[1] ?? null,
                $matches[3] ?? null,
                $matches[4] ?? null,
            ];
        }

        return null;
    }

    private static function getComplexDate(string $string, bool $two_digit_year = FALSE): ?array
    {
        $year_pattern = '(\d{4})';
        if ($two_digit_year) {
          $year_pattern = '\'(\d{2})';
        }
        // Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015
        preg_match(
            '/(?:(?:'.implode('|', self::DAYS).'|'.implode('|', self::SHORT_DAYS).')[ ,\-_\/]*)?(\d?\d)[ ,\-_\/]*(?:'.implode('|', self::ORDINALS).')?[ ,\-_\/(?:of)]*('.implode('|', self::MONTHS).'|'.implode('|', self::SHORT_MONTHS).')\b(?:[ ,\-_\/]+(?:' .$year_pattern . '))?/i',
            $string,
            $matches
        );
        $day = $matches[1] ?? null;
        $month = $matches[2] ?? null;
        $year = $matches[3] ?? $matches[4] ?? null;
        $year = $year ?: $matches[4] ?? null;
        if ($month !== null) {
            $month = self::getMonthNumber((string) $month);
        }

        // Match dates: March 1st 2015; March 1 2015; March-1st-2015
        preg_match(
            '/('.implode('|', self::MONTHS).'|'.implode('|', self::SHORT_MONTHS).')\b[ ,\-_\/]*(\d?\d)[ ,\-_\/]*(?:'.implode('|', self::ORDINALS).')?[ ,\-_\/]+(?:' . $year_pattern . ')/i',
            $string,
            $matches
        );
        if ($matches) {
            if ($month === null && isset($matches[1])) {
                $month = self::getMonthNumber($matches[1]);
            }

            $day = $day ?? $matches[2] ?? null;

            $year = ($year ?? $matches[3] ?? null) ?: $matches[4] ?? null;
        }

        return [
            $day,
            $month,
            $year,
        ];
    }

    /**
     * @return int[]|null
     */
    private static function getIsoDate(string $string): ?array
    {

        // Match dates: 2025-06-30 or 2025/06/30 or 2025 06 30 as well as
        // more unusual combinations likely in directories like 2025-06/30/19
        preg_match('/(\d{4})[.\-\/ ](\d{2})[.\-\/ ](\d{2})/', $string, $matches);
        if (($matches[1] ?? null) !== null && ($matches[2] ?? null) !== null && ($matches[3] ?? null) !== null) {
            return [
                $matches[3] ?? null,
                $matches[2] ?? null,
                $matches[1] ?? null,
            ];
        }

        return null;
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
        preg_match(
            '/('.implode('|', self::MONTHS).')\b/i',
            $string,
            $matches_month_word
        );
        if ($matches_month_word && $matches_month_word[1]) {
            $month = array_search(strtolower($matches_month_word[1]), self::MONTHS);
        }
        // Match short month names
        if ($month === null) {
            preg_match(
                '/('.implode('|', self::SHORT_MONTHS).')\b/i',
                $string,
                $matches_month_word
            );
            if ($matches_month_word && $matches_month_word[1]) {
                $month = array_search(strtolower($matches_month_word[1]), self::SHORT_MONTHS);
            }
        }

        return $month !== null ? ++$month : null;
    }
}
