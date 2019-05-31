<?php
namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $this->encodeUserPassword($entity);

        parent::persistEntity($entity);
    }

    /**
     * @param User $entity
     */
    protected function updateEntity($entity)
    {
        $this->encodeUserPassword($entity);

        parent::updateEntity($entity);
    }

    /**
     * @param User $entity
     */
    protected function encodeUserPassword($entity): void
    {
        $entity->setPassword( $this->passwordEncoder->encodePassword($entity, $entity->getPassword()) );
    }

}
