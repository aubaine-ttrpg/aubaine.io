<?php

namespace App\Repository;

use App\Entity\OldSkills;
use App\Enum\Ability;
use App\Enum\SkillCategory;
use App\Enum\SkillRange;
use App\Enum\SkillType;
use App\Enum\Source;
use App\Enum\SkillDuration;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OldSkills>
 */
class OldSkillsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OldSkills::class);
    }

    public function save(OldSkills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OldSkills $entity, bool $flush = false): void
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
     *     tags?: list<Tag>
     * } $filters
     *
     * @return list<OldSkills>
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
            $qb
                ->leftJoin('s.tags', 'tags')
                ->andWhere('tags IN (:tags)')
                ->setParameter('tags', $filters['tags'])
                ->addSelect('tags')
                ->distinct();
        }

        return $qb
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
