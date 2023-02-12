<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractApiController
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $users = $this->doctrine->getRepository(User::class)->findAll();

        return $this->respond($users);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     *
     * @return Response
     */
    public function createAction(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->buildForm(UserType::class);
        $form->handleRequest($request);

        // Validate
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /* @var User $user */
        $user = $form->getData();

        // Hashing password
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        // Saving user
        $userRepository = new UserRepository($this->doctrine);
        $userRepository->save($user, true);

        return $this->respond($user);
    }

    public function editAction(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        [
            $user,
            $userRepository
        ] = $this->findUser($request);

        $form = $this->buildForm(UserType::class, $user);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /* @var User $user */
        $userFormData = $form->getData();

        // Hashing password
        $hashedPassword = $passwordHasher->hashPassword($user, $userFormData->getPassword());
        $user->setPassword($hashedPassword);

        // Saving user
        $userRepository->save($user, true);

        return $this->respond($user);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        [
            $user,
            $userRepository
        ] = $this->findUser($request);
        $userRepository->remove($user, true);

        return $this->respond('User deleted successfully');
    }

    /**
     * @param Request $request
     *
     * @return array{ 0: User, 1: UserRepository }
     */
    private function findUser(Request $request): array
    {
        $userId = $request->get('userId');

        $userRepository = new UserRepository($this->doctrine);
        $user = $userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return [
            $user,
            $userRepository
        ];
    }
}