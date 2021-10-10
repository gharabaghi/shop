<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private QueryBuilder $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
        $this->initailizeQueryBuilderInstance();
    }

    /**
     * Initializing $this->qb
     *
     * @return ProductRepository
     */
    public function initailizeQueryBuilderInstance()
    {
        $this->qb = $this->createQueryBuilder('p');
        return $this;
    }

    /**
     * Add andWhere to $this->qb
     *
     * @return ProductRepository
     */
    public function qbFindAllAvailableProducts()
    {
        $this->qb = $this->qb->andWhere('p.count > 0');
        return $this;
    }

    /**
     * Add orderBy to $this->qb
     *
     * @return ProductRepository
     */
    public function qbOrderBy(string $col, $order = null)
    {
        $col = 'p.'.$col;
        $this->qb = $this->qb->orderBy($col, $order);
        return $this;
    }

    /**
     * Add limit to $this->qb
     *
     * @return ProductRepository
     */
    public function qbLimit(int $maxResult)
    {
        $this->qb = $this->qb->setMaxResults($maxResult);
        return $this;
    }

    /**
     * executes the Product[]
     *
     * @return ProductRepository
     */
    public function qbGetResult()
    {
        return $this->qb->getQuery()->getResult();
    }
}
