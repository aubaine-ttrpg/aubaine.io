<?php

namespace App\Repository;

use App\Entity\Skills;
use App\Enum\Ability;
use App\Enum\SkillCategory;
use App\Enum\SkillRange;
use App\Enum\SkillType;
use App\Enum\Source;
use App\Enum\SkillDuration;
use App\Enum\SkillTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skills>
 */
class SkillsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skills::class);
    }

    public function save(Skills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Skills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array{
     *     category?: list<SkillCategory>,
     *     type?: list<SkillType>,
     *     source?: list<Source>,
     *     range?: list<SkillRange>,
     *     duration?: list<SkillDuration>,
     *     abilities?: list<Ability>,
     *     tags?: list<SkillTag>
     * } $filters
     *
     * @return list<Skills>
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('s');

        $mapEnumValues = static fn (array $values): array => array_map(
            static fn ($v) => $v instanceof \BackedEnum ? $v->value : (string) $v,
            $values
        );

        if (!empty($filters['category'])) {
            $qb->andWhere('s.category IN (:cats)')->setParameter('cats', $mapEnumValues($filters['category']));
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('s.type IN (:types)')->setParameter('types', $mapEnumValues($filters['type']));
        }

        if (!empty($filters['source'])) {
            $qb->andWhere('s.source IN (:sources)')->setParameter('sources', $mapEnumValues($filters['source']));
        }

        if (!empty($filters['range'])) {
            $qb->andWhere('s.range IN (:ranges)')->setParameter('ranges', $mapEnumValues($filters['range']));
        }

        if (!empty($filters['duration'])) {
            $qb->andWhere('s.duration IN (:durations)')->setParameter('durations', $mapEnumValues($filters['duration']));
        }

        if (!empty($filters['abilities'])) {
            foreach ($filters['abilities'] as $idx => $ability) {
                $qb
                    ->andWhere(sprintf('s.abilities LIKE :ability_%d', $idx))
                    ->setParameter(sprintf('ability_%d', $idx), '%"' . $ability->value . '"%');
            }
        }

        if (!empty($filters['tags'])) {
            $tagExpr = $qb->expr()->orX();
            foreach ($filters['tags'] as $idx => $tag) {
                $tagExpr->add(sprintf('s.tags LIKE :tag_%d', $idx));
                $qb->setParameter(sprintf('tag_%d', $idx), '%"' . $tag->value . '"%');
            }
            $qb->andWhere($tagExpr);
        }

        return $qb
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
