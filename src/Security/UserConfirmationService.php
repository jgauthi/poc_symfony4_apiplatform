<?php
namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $confirmationToken
     */
    public function confirmUser(string $confirmationToken): void
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if(!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabled(true)
             ->setConfirmationToken(null);
        $this->entityManager->flush();
    }
}
