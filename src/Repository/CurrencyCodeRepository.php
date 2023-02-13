<?php

namespace App\Repository;

use App\Entity\CurrencyCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyCode>
 *
 * @method CurrencyCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyCode[]    findAll()
 * @method CurrencyCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyCode::class);
    }

    /**
     * @param CurrencyCode $entity
     * @param bool $flush
     * @return void
     */
    public function save(CurrencyCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CurrencyCode $entity
     * @param bool $flush
     * @return void
     */
    public function remove(CurrencyCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return float|int|mixed|string
     */
    public function deleteAll()
    {
        return $this->createQueryBuilder('cc')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
