<?php

namespace App\Repository;

use App\Entity\CurrencyDynamic;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyDynamic>
 *
 * @method CurrencyDynamic|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyDynamic|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyDynamic[]    findAll()
 * @method CurrencyDynamic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyDynamicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyDynamic::class);
    }

    public function save(CurrencyDynamic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CurrencyDynamic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDateAndCode($date, $code): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('value', 'val');
        $rsm->addScalarResult('diff_value', 'diff');

        $datetime = new DateTime($date);
        $date2weeksAgo = $datetime->modify('-2 weeks')->format('Y-m-d');

        return $this->_em
            ->createNativeQuery(
                "SELECT 
                    value, 
                    (lag(value, 1) over (order by id)) - value as diff_value
                FROM currency_dynamic
                WHERE date BETWEEN :dateFrom AND :dateTo AND currency_id = :code
                ORDER BY date DESC
                LIMIT 1",
                $rsm
            )
            ->setParameters([
                'dateFrom' => $date2weeksAgo,
                'dateTo'   => $date,
                'code'     => $code
            ])
            ->getArrayResult();
    }
}
