<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiArticleController extends AbstractController
{

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @Route("/api/article", methods={"GET","HEAD"})
     */
    public function index(ArticleRepository $articleRepository, Request $request, CategoryRepository $categoryRepository): Response
    {

        $limit = $request->get('limit') ?? 15;
        $current_page = $request->get('page') ?? 1;
        $offset = $limit*(($current_page === 1) ? 0 : $current_page);

        $category = null;
        if($request->get('category'))
        {
            $category = $categoryRepository->findOneByName($request->get('category'));
        }

        $total = $articleRepository->createQueryBuilder('a')->select('count(a.id)');
        $articles = $articleRepository->createQueryBuilder('a');

        if($category)
        {
            $total = $total->andWhere('a.category = :val')->setParameter('val', $category->getId());
            $articles = $articles->andWhere('a.category = :val')->setParameter('val', $category->getId());
        }

        $total = $total->getQuery()->getSingleScalarResult();
        $articles = $articles->setFirstResult($offset)
                            ->setMaxResults($limit)
                            ->getQuery()
                            ->getResult();

        $data = []; $count = 0;
        foreach($articles as $article)
        {
            $data[$count]['id'] = $article->getId();
            $data[$count]['name'] = $article->getName();
            $data[$count]['description'] = $article->getDescription();
            $data[$count]['category'] = $article->getCategory()->getName();
            $count++;
        }

        return $this->json([
            'data' => $data,
            'total' => $total,
            'per_page' => $limit,
            'current_page' => $current_page,
            'last_page' => ceil($total/$limit)
        ]);
    }

    /**
     * @Route("/api/article", methods={"POST"})
     */
    public function new(Request $request, ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank()
            ],
            'description' => [
                new Assert\NotBlank(),
            ],
            'category' => [
                new Assert\NotBlank(),
            ],
        ]);
        $inputs = [
            'name' => $request->get('name'), 
            'description' => $request->get('description'),
            'category' => $request->get('category'),
        ];
        $validationResult = $this->validator->validate($inputs, $constraints);

        if(count($validationResult))
        {
            return $this->json([
                'error' => $validationResult[0]->getPropertyPath() . ' ' . $validationResult[0]->getMessage(),
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = $categoryRepository->findOneByName($request->get('category'));

        if(!$category)
        {
            $category = new Category();
            $category->setName($request->get('category'));
            $categoryRepository->add($category, true);
        }

        $article = new Article();
            $article->setName($request->get('name'));
            $article->setDescription($request->get('description'));
            $article->setCategory($category);

        $articleRepository->add($article, true);


        $data['id'] = $article->getId();
        $data['name'] = $article->getName();
        $data['description'] = $article->getDescription();
        $data['category'] = $article->getCategory()->getName();

        return $this->json([
            'data' => $data
        ],Response::HTTP_CREATED);
    }


    /**
     * @Route("/api/article/{id}", methods={"POST"})
     */
    public function update(Request $request, ArticleRepository $articleRepository, CategoryRepository $categoryRepository, int $id): Response
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank()
            ],
            'description' => [
                new Assert\NotBlank(),
            ],
            'category' => [
                new Assert\NotBlank(),
            ],
        ]);
        $inputs = [
            'name' => $request->get('name'), 
            'description' => $request->get('description'),
            'category' => $request->get('category'),
        ];
        $validationResult = $this->validator->validate($inputs, $constraints);
        if(count($validationResult))
        {
            return $this->json([
                'error' => $validationResult[0]->getPropertyPath() . ' ' . $validationResult[0]->getMessage(),
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        $article = $articleRepository->find($id);

        if (!$article) {
            return $this->json([
                'error' => 'Article with id: ' . $id . ' not found',
            ],Response::HTTP_NOT_FOUND);
        }

        $category = $categoryRepository->findOneByName($request->get('category'));

        if(!$category)
        {
            $category = new Category();
            $category->setName($request->get('category'));
            $categoryRepository->add($category, true);
        }

        $article = new Article();
            $article->setName($request->get('name'));
            $article->setDescription($request->get('description'));
            $article->setCategory($category);

        $articleRepository->add($article, true);

        $data = [];
        $data['id'] = $article->getId();
        $data['name'] = $article->getName();
        $data['description'] = $article->getDescription();
        $data['category'] = $article->getCategory()->getName();

        return $this->json([
            'data' => $data
        ],Response::HTTP_ACCEPTED );
    }


    /**
     * @Route("/api/article/{id}", methods={"GET","HEAD"})
     */
    public function show(int $id, ArticleRepository $articleRepository): Response
    {   

        $article = $articleRepository->find($id);

        if (!$article) {
            return $this->json([
                'error' => 'Article with id: ' . $id . ' not found',
            ],Response::HTTP_NOT_FOUND);
        }
        $data = [];
        $data['id'] = $article->getId();
        $data['name'] = $article->getName();
        $data['description'] = $article->getDescription();
        $data['category'] = $article->getCategory()->getName();

        return $this->json([
            'data'  => $data
       ]);
    }

    /**
     * @Route("/api/article/{id}", methods={"DELETE"})
     */
    public function destroy(int $id, ArticleRepository $articleRepository): Response
    {   
        $article = $articleRepository->find($id);
        $articleRepository->remove($article, true);

        return $this->json([
            'data'  => 'OK'
       ]);

    }



}
