<?php

declare(strict_types=1);

use acurrieclark\DateStringUtilities\DateInStringFinder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DateInStringFinderTest extends TestCase
{
    /**
     * @dataProvider dataStringDataProvider
     *
     * @param int[] $expected
     */
    public function testDateInStringFinder(string $string, array $expected): void
    {
        $this->assertSame($expected, DateInStringFinder::find($string));
    }

    public function dataStringDataProvider(): array
    {
        return [
            [
                'this will be achieved before 10/02/2019',
                [
                    'day' => 10,
                    'month' => 2,
                    'year' => 2019,
                ],
            ],
            [
                'The painters said they had completed the job on 4th March 2020',
                [
                    'day' => 4,
                    'month' => 3,
                    'year' => 2020,
                ],
            ],
            [
                'This building was cleaned on the 8th of October 2006 after a huge storm',
                [
                    'day' => 8,
                    'month' => 10,
                    'year' => 2006,
                ],
            ],
            [
                'This building was cleaned on the 8th of October after a huge storm',
                [
                    'day' => 8,
                    'month' => 10,
                    'year' => null,
                ],
            ],
            [
                'This building was cleaned at the end of October 1983 after a huge storm',
                [
                    'day' => null,
                    'month' => 10,
                    'year' => 1983,
                ],
            ],
            [
                'HOUSE 34 STREET 1: MONDAY 16th JULY 2018',
                [
                    'day' => 16,
                    'month' => 7,
                    'year' => 2018,
                ],
            ],
            [
                '30-12-11',
                [
                    'day' => 30,
                    'month' => 12,
                    'year' => 11,
                ],
            ],
            [
                'uploads/2025-06/example.pdf',
                [
                    'day' => null,
                    'month' => 06,
                    'year' => 2025,
                ],
            ],
            [
                'uploads/2025-06/17/example.pdf',
                [
                    'day' => 17,
                    'month' => 06,
                    'year' => 2025,
                ],
            ],
            [
                'uploads/2025-06/example.pdf',
                [
                    'day' => null,
                    'month' => 06,
                    'year' => 2025,
                ],
            ],
            [
                '2025-06-17',
                [
                    'day' => 17,
                    'month' => 06,
                    'year' => 2025,
                ],
            ],
            [
                '1 2 1985',
                [
                    'day' => 01,
                    'month' => 02,
                    'year' => 1985,
                ],
            ],
            [
                'There were no more than 15. It started on 10.12.1850',
                [
                    'day' => 10,
                    'month' => 12,
                    'year' => 1850,
                ],
            ],
            [
                '10.12.1850',
                [
                    'day' => 10,
                    'month' => 12,
                    'year' => 1850,
                ],
            ],
            [
                '5 12 85',
                [
                    'day' => 05,
                    'month' => 12,
                    'year' => 85,
                ],
            ],
            [
                '5 12/85',
                [
                    'day' => null,
                    'month' => null,
                    'year' => null,
                ],
            ],
            [
                '5-12/85',
                [
                    'day' => null,
                    'month' => null,
                    'year' => null,
                ],
            ],
            [
                'Marshgate Business Centre, 10-12 Marshgate Lane',
                [
                    'day' => null,
                    'month' => null,
                    'year' => null,
                ],
            ],
            [
                '5 October 2012',
                [
                    'day' => 05,
                    'month' => 10,
                    'year' => 2012,
                ],
            ],
            [
                'October 5 \'12',
                [
                    'day' => 05,
                    'month' => 10,
                    'year' => 12,
                ],
            ],
            [
                'MAY 5th 1985',
                [
                    'day' => 05,
                    'month' => 05,
                    'year' => 1985,
                ],
            ],
            [
                '1st March \'81',
                [
                    'day' => 01,
                    'month' => 03,
                    'year' => 81,
                ],
            ],
            [
                'March 1st',
                [
                    'day' => 01,
                    'month' => 03,
                    'year' => null,
                ],
            ],
            [
                'Apr \'20',
                [
                    'day' => null,
                    'month' => 04,
                    'year' => 20,
                ],
            ],
            [
                'Apr 20th',
                [
                    'day' => 20,
                    'month' => 04,
                    'year' => null,
                ],
            ],
            [
                'Sunday, 24 June',
                [
                    'day' => 24,
                    'month' => 06,
                    'year' => null,
                ],
            ],
            [
                'Mon 2nd July',
                [
                    'day' => 02,
                    'month' => 07,
                    'year' => null,
                ],
            ],
            [
                '"There is no date in this string", said Jane',
                [
                    'day' => null,
                    'month' => null,
                    'year' => null,
                ],
            ],
        ];
    }
}
