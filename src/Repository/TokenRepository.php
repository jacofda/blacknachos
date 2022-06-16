<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Token;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Token>
 *
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function add(Token $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Token $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function setNewTokenForUser(User $user) : Token
    {   
        $token = new Token();
            $token->setUser($user);
            $token->setToken($this->generateRandomString());
            $token->setExpiredAt((new \DateTimeImmutable())->modify('+1 week'));

        $this->add($token, true);
        return $token;
    }

    public function generateRandomString($length = 20) : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
      

   /**
    * @return token with expiring gte now
    */
   public function latestValidToken($value): ?Token
   {
       return $this->createQueryBuilder('t')
           ->andWhere('t.user = :val')
           ->andWhere('t.expired_at > :date')
           ->setParameter('val', $value)
           ->setParameter('date', new \DateTimeImmutable())
           ->orderBy('t.id', 'DESC')
           ->setMaxResults(1)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

//    public function findOneBySomeField($value): ?Token
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
