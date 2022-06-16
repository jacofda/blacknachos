<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(#[CurrentUser] ?User $user, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {   
        $email = $request->get('username');
        $plaintextPassword = $request->get('password');
        $user = $userRepository->findOneByEmail($email);
        if($user)
        {
            if (!$passwordHasher->isPasswordValid($user, $plaintextPassword)) {
                return $this->json([
                    'error' => 'wrong password',
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            $token = 'ciaorandomtoken';

            return $this->json([
                 'token' => $token,
            ]);
        }

        return $this->json([
            'error' => 'could not find a user with '.$request->get('username')
       ], Response::HTTP_UNAUTHORIZED);

    }


}
