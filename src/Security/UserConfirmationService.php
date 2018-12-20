<?php
namespace App\Security;

use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

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
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $confirmationToken): void
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if(!$user) {
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true)
             ->setConfirmationToken(null);
        $this->entityManager->flush();
    }
}
