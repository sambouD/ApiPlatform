<?php

namespace App\Repository;

use App\Entity\Adherent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Adherent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adherent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adherent[]    findAll()
 * @method Adherent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdherentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adherent::class);
    }

    public function nbPretsParAdherent(){
        $query = $this->createQueryBuilder('a')
        ->select('a.id, a.nom, a.prenom, count(p.id) as nbPrets')
        ->join('a.prets', 'p')
        ->groupBy('a')
        ->orderBy('nbPrets', 'DESC');
        return $query->getQuery()->getResult();
    }
}
