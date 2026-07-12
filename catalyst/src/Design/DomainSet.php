<?php

declare(strict_types=1);

namespace App\Design;

/**
 * The zero, one, or two domains a skill node carries, and how that renders.
 *
 * Empty means "Neutre". One domain is a solid border colour. Two domains are a
 * 135-degree split border. This recreates the token helper `frame()` as a small
 * value object instead of a string-keyed function.
 */
final readonly class DomainSet
{
    /** @var list<Domain> */
    private array $domains;

    public function __construct(Domain ...$domains)
    {
        $this->domains = array_values(\array_slice($domains, 0, 2));
    }

    /**
     * @param list<string> $keys domain keys as stored in tree JSON; unknown
     *                           keys are ignored so a bad value never crashes a render
     */
    public static function fromKeys(array $keys): self
    {
        $domains = [];
        foreach ($keys as $key) {
            $domain = Domain::tryFrom($key);
            if ($domain instanceof Domain) {
                $domains[] = $domain;
            }
        }

        return new self(...$domains);
    }

    public function isEmpty(): bool
    {
        return [] === $this->domains;
    }

    /** @return list<Domain> */
    public function all(): array
    {
        return $this->domains;
    }

    public function primaryColor(): string
    {
        return $this->domains[0]->color() ?? Domain::NEUTRAL_COLOR;
    }

    /** CSS `background`/`border-image` value: solid colour or a 135-degree split. */
    public function borderCss(): string
    {
        return match (\count($this->domains)) {
            0 => Domain::NEUTRAL_COLOR,
            1 => $this->domains[0]->color(),
            default => \sprintf(
                'linear-gradient(135deg, %s 0 50%%, %s 50%% 100%%)',
                $this->domains[0]->color(),
                $this->domains[1]->color(),
            ),
        };
    }

    /** @return list<string> */
    public function labels(string $locale = 'en'): array
    {
        if ([] === $this->domains) {
            return ['fr' === $locale ? Domain::NEUTRAL_LABEL_FR : Domain::NEUTRAL_LABEL_EN];
        }

        return array_map(static fn (Domain $domain): string => $domain->label($locale), $this->domains);
    }
}
