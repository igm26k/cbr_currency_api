<?php

namespace App\Repository;

use App\Entity\CurrencyDynamic;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public function findByDateAndCode($date, $code): ?array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('value', 'val');
        $rsm->addScalarResult('diff_value', 'diff');

        $datetime = new DateTime($date);
        $day = $datetime->format('D');

        if ($day === 'Sun') {
            $datetime->modify('-2 day');
        }
        elseif ($day === 'Mon') {
            $datetime->modify('-3 day');
        }
        else {
            $datetime->modify('-1 day');
        }

        $dateInPast = $datetime->format('Y-m-d');

        return $this->_em
            ->createNativeQuery(
                "SELECT 
                    date,
                    value, 
                    (lag(value, 1) over (order by id)) - value as diff_value
                FROM currency_dynamic
                WHERE date BETWEEN :dateFrom AND :dateTo 
                  AND currency_id = :code
                ORDER BY date DESC
                LIMIT 1",
                $rsm
            )
            ->setParameters([
                'dateFrom' => $dateInPast,
                'dateTo'   => $date,
                'code'     => $code
            ])
            ->getOneOrNullResult();
    }

    /**
     * @param $code
     * @return CurrencyDynamic|null
     * @throws NonUniqueResultException
     */
    public function findLastItemByCode($code)
    {
        return $this->createQueryBuilder('cd')
            ->andWhere('cd.CurrencyID = :code')
            ->setParameter('code', $code)
            ->orderBy('cd.Date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
