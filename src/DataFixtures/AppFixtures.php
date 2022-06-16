<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {


        $user = new User();
        $user->setEmail('info@blacknachos.com');

        $password = $this->hasher->hashPassword($user, 'blacknachos');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();


        $categories = [];
        for ($i = 1; $i < 11; $i++) {
            $category = new Category();
            $category->setName('CategoryName '.$i);
            $manager->persist($category);
            $categories[] = $category;
        }


        for ($i = 1; $i < 201; $i++) {
            $article = new Article();
            $article->setName('ArticleName '.$i);
            $article->setDescription('Article Long Description '.$i);
            $article->setCategory($categories[random_int(0,9)]);
            $manager->persist($article);
        }


        $manager->flush();
    }
}
