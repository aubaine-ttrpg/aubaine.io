<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use IntlDateFormatter;
use Psr\Clock\ClockInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Renders a past instant as a coarse "il y a ..." label and an absolute
 * tooltip, both localized. Coarse by design: it buckets the elapsed time to a
 * single unit rather than an exact duration.
 */
final class RelativeTimeFormatter
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly TranslatorInterface $translator,
        private readonly LocaleAwareInterface $localeAware,
    ) {
    }

    /**
     * A localized "il y a X <unit>" (or "à l'instant" under a minute).
     */
    public function ago(DateTimeImmutable $then): string
    {
        $seconds = max(0, $this->clock->now()->getTimestamp() - $then->getTimestamp());

        if ($seconds < 60) {
            return $this->translator->trans('time.ago.just_now');
        }

        [$unit, $count] = $this->bucket($seconds);

        return $this->translator->trans(
            \sprintf('time.ago.%s_%s', $unit, 1 === $count ? 'one' : 'other'),
            ['%count%' => $count],
        );
    }

    /**
     * The exact instant, formatted for the current locale (tooltip use).
     */
    public function absolute(DateTimeImmutable $when): string
    {
        $formatter = new IntlDateFormatter(
            $this->localeAware->getLocale(),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::SHORT,
        );

        $formatted = $formatter->format($when);

        return false === $formatted ? $when->format('Y-m-d H:i') : $formatted;
    }

    /**
     * @return array{0: string, 1: int} unit key fragment and its count
     */
    private function bucket(int $seconds): array
    {
        $minutes = intdiv($seconds, 60);
        if ($minutes < 60) {
            return ['minutes', $minutes];
        }

        $hours = intdiv($minutes, 60);
        if ($hours < 24) {
            return ['hours', $hours];
        }

        $days = intdiv($hours, 24);
        if ($days < 7) {
            return ['days', $days];
        }
        if ($days < 30) {
            return ['weeks', intdiv($days, 7)];
        }
        if ($days < 365) {
            return ['months', intdiv($days, 30)];
        }

        return ['years', intdiv($days, 365)];
    }
}
