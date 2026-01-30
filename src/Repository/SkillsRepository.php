<?php

namespace App\Repository;

use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Enum\SkillCategory;
use App\Entity\Skills;
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
     *     ability?: list<Ability>,
     *     aptitude?: list<Aptitude>,
     *     ultimate?: bool
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
            $qb->andWhere('s.category IN (:categories)')->setParameter('categories', $mapEnumValues($filters['category']));
        }

        if (!empty($filters['ability'])) {
            $qb->andWhere('s.ability IN (:abilities)')->setParameter('abilities', $mapEnumValues($filters['ability']));
        }

        if (!empty($filters['aptitude'])) {
            $qb->andWhere('s.aptitude IN (:aptitudes)')->setParameter('aptitudes', $mapEnumValues($filters['aptitude']));
        }

        if (!empty($filters['ultimate'])) {
            $qb->andWhere('s.ultimate = :ultimate')->setParameter('ultimate', true);
        }

        return $qb
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
