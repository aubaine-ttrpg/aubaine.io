<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Support\RelativeTimeFormatter;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RelativeTimeFormatterTest extends TestCase
{
    /**
     * @return iterable<string, array{int, string}>
     */
    public static function elapsed(): iterable
    {
        yield 'under a minute' => [30,             'time.ago.just_now|'];
        yield 'one minute' => [60,             'time.ago.minutes_one|1'];
        yield 'many minutes' => [5 * 60,         'time.ago.minutes_other|5'];
        yield 'one hour' => [3600,           'time.ago.hours_one|1'];
        yield 'many hours' => [3 * 3600,       'time.ago.hours_other|3'];
        yield 'one day' => [86400,          'time.ago.days_one|1'];
        yield 'many days' => [3 * 86400,      'time.ago.days_other|3'];
        yield 'one week' => [7 * 86400,      'time.ago.weeks_one|1'];
        yield 'two weeks' => [14 * 86400,     'time.ago.weeks_other|2'];
        yield 'one month' => [30 * 86400,     'time.ago.months_one|1'];
        yield 'many months' => [90 * 86400,     'time.ago.months_other|3'];
        yield 'one year' => [365 * 86400,    'time.ago.years_one|1'];
        yield 'many years' => [2 * 365 * 86400, 'time.ago.years_other|2'];
    }

    #[DataProvider('elapsed')]
    public function testAgoBucketsAndPluralizes(int $secondsAgo, string $expected): void
    {
        $now = new DateTimeImmutable('2026-07-21T12:00:00+00:00');
        $formatter = new RelativeTimeFormatter(new MockClock($now), $this->echoTranslator(), $this->echoTranslator());

        $then = $now->modify(\sprintf('-%d seconds', $secondsAgo));

        self::assertSame($expected, $formatter->ago($then));
    }

    public function testFutureInstantClampsToJustNow(): void
    {
        $now = new DateTimeImmutable('2026-07-21T12:00:00+00:00');
        $formatter = new RelativeTimeFormatter(new MockClock($now), $this->echoTranslator(), $this->echoTranslator());

        self::assertSame('time.ago.just_now|', $formatter->ago($now->modify('+1 hour')));
    }

    public function testAbsoluteFormatsForLocale(): void
    {
        $now = new DateTimeImmutable('2026-07-21T12:00:00+00:00');
        $formatter = new RelativeTimeFormatter(new MockClock($now), $this->echoTranslator(), $this->echoTranslator());

        self::assertStringContainsString('2026', $formatter->absolute($now));
    }

    /**
     * A translator that echoes the resolved key and count, so the test asserts
     * which key the formatter picked rather than the catalog wording.
     */
    private function echoTranslator(): TranslatorInterface&LocaleAwareInterface
    {
        return new class implements TranslatorInterface, LocaleAwareInterface {
            /** @param array<array-key, mixed> $parameters */
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                $count = $parameters['%count%'] ?? '';

                return $id.'|'.(\is_scalar($count) ? (string) $count : '');
            }

            public function getLocale(): string
            {
                return 'fr';
            }

            public function setLocale(string $locale): void
            {
            }
        };
    }
}
