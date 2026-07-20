<?php

declare(strict_types=1);

namespace App\Command;

use App\Book\BookType;
use App\Design\Characteristic;
use App\Design\Domain;
use App\Design\NodeType;
use App\Design\Paper;
use App\Page\PageTypeRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use Twig\Environment;

/**
 * Dumps the design system's single-source-of-truth fixtures for Sigil's Storybook:
 * the real print HTML for every page type (rendered from each type's defaultData
 * through the same Twig templates the PDF uses) and design-data.json (the game
 * colours + labels, straight from the PHP enums). The Storybook stories load these,
 * so a story can never drift from the Twig macros or the enums.
 *
 * Run after building assets (`npm run dev`): the print templates resolve cover art,
 * node icons, and paper textures through the Encore manifest.
 */
#[AsCommand(
    name: 'app:design:dump',
    description: 'Render print fixtures + dump enum colours into sigil/fixtures for Storybook.',
)]
final class DumpDesignCommand extends Command
{
    /** Cover pages need the book-level stamp PageViewFactory injects; synthesised here. */
    private const array COVER_CONTEXT = [
        'bookTitle' => 'Aubaine',
        'copyright' => '© Aubaine',
        'versionShort' => 'v0.1',
        'versionFull' => 'v0.1',
    ];

    public function __construct(
        private readonly Environment $twig,
        private readonly PageTypeRegistry $registry,
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();
        $fixtures = \dirname($this->projectDir).'/sigil/fixtures';

        $io->title('Dumping Sigil design fixtures');

        $failures = $this->dumpPrintPages($fs, $fixtures.'/print', $io);
        $this->dumpDesignData($fs, $fixtures.'/design-data.json', $io);

        if ([] !== $failures) {
            $io->error(\sprintf('%d page type(s) failed to render: %s', \count($failures), implode(', ', $failures)));

            return Command::FAILURE;
        }

        $io->success('Fixtures written to '.$fixtures);

        return Command::SUCCESS;
    }

    /**
     * Renders every registered page type to its real print HTML. Returns the keys
     * that failed (reported, never swallowed) so the command can exit non-zero.
     *
     * @return list<string>
     */
    private function dumpPrintPages(Filesystem $fs, string $dir, SymfonyStyle $io): array
    {
        $failures = [];
        foreach ($this->registry->all() as $type) {
            $view = $type->buildViewModel($type->defaultData());
            if ('covers' === $type->category()) {
                $view = array_merge($view, self::COVER_CONTEXT, ['bookTypeLabel' => BookType::Archetype->labelKey()]);
            }
            $view['pageId'] = $type->key();

            try {
                $html = $this->twig->render($type->template(), $view);
            } catch (Throwable $e) {
                $io->warning(\sprintf('%s: %s', $type->key(), $e->getMessage()));
                $failures[] = $type->key();
                continue;
            }

            $fs->dumpFile($dir.'/'.$type->key().'.html', $html);
            $io->writeln('  <info>✓</info> print/'.$type->key().'.html');
        }

        return $failures;
    }

    private function dumpDesignData(Filesystem $fs, string $path, SymfonyStyle $io): void
    {
        $data = [
            'domains' => [
                ...array_map(static fn (Domain $d): array => [
                    'key' => $d->value,
                    'color' => $d->color(),
                    'labelFr' => $d->labelFr(),
                    'labelEn' => $d->labelEn(),
                ], Domain::cases()),
                [
                    'key' => 'neutral',
                    'color' => Domain::NEUTRAL_COLOR,
                    'labelFr' => Domain::NEUTRAL_LABEL_FR,
                    'labelEn' => Domain::NEUTRAL_LABEL_EN,
                ],
            ],
            'characteristics' => array_map(static fn (Characteristic $c): array => [
                'key' => $c->value,
                'color' => $c->color(),
                'iconName' => $c->iconName(),
                'labelFr' => $c->labelFr(),
                'labelEn' => $c->labelEn(),
            ], Characteristic::cases()),
            'papers' => array_map(static fn (Paper $p): array => [
                'key' => $p->value,
                'color' => $p->color(),
                'textureFile' => $p->textureFile(),
                'labelFr' => $p->labelFr(),
                'labelEn' => $p->labelEn(),
            ], Paper::cases()),
            'nodeTypes' => array_map(static fn (NodeType $t): array => [
                'key' => $t->value,
                'shapeKey' => $t->shapeKey(),
                'labelFr' => $t->labelFr(),
                'labelEn' => $t->labelEn(),
            ], NodeType::cases()),
        ];

        $json = json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
        $fs->dumpFile($path, $json."\n");
        $io->writeln('  <info>✓</info> design-data.json');
    }
}
