<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function save(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByEmail(string $email): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByRole(string $roleName): int
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.role', 'r') // Assuming 'role' is a relation in Utilisateur
            ->where('r.nom_role = :roleName') // Adjust based on your Role entity's property
            ->setParameter('roleName', $roleName)
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countByGender(string $gender): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.genre = :gender')
            ->setParameter('gender', $gender)
            ->getQuery()
            ->getSingleScalarResult();
    }
    

    public function findBySearchQuery(string $searchQuery)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nom LIKE :query OR u.prenom LIKE :query OR u.email LIKE :query')
            ->setParameter('query', '%'.$searchQuery.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all users sorted by the specified field.
     *
     * @param string $sortBy Field to sort by (e.g., 'name' or 'surname')
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */
    public function findAllSorted(string $sortBy): array
    {
        $qb = $this->createQueryBuilder('u');

        // Sort by name
        if ($sortBy === 'name') {
            $qb->orderBy('u.nom', 'ASC');
        }
        // Sort by surname
        elseif ($sortBy === 'surname') {
            $qb->orderBy('u.prenom', 'ASC');
        }
        // Default to sorting by name
        else {
            $qb->orderBy('u.nom', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllWithRoles(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r') // Include the role in the result
            ->getQuery()
            ->getResult();
    }



}