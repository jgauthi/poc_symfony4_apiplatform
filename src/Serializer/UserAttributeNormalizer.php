<?php
namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\{CircularReferenceException, InvalidArgumentException, LogicException};
use Symfony\Component\Serializer\Normalizer\{ContextAwareNormalizerInterface, NormalizableInterface};
use Symfony\Component\Serializer\{SerializerAwareInterface, SerializerAwareTrait};

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserAttributeNormalizer constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $context options that normalizers have access to
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isUserHimSelf($object)) {
            $context['groups'][] = 'ownerFields';
        }

        // Continue the normalization
        return $this->passOn($object, $format, $context);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function isUserHimSelf(User $user): bool
    {
        return ($user->getUsername() === $this->tokenStorage->getToken()->getUsername());
    }

    /**
     * @param $object
     * @param string $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    private function passOn($object, string $format, array $context)
    {
        if (!method_exists($this->serializer, 'normalize')) {
            throw new \LogicException("Cannot normalize object \"{$object}\" because the injected normalizer is not a normalizer.");
        }

        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;
        return $this->serializer->normalize($object, $format, $context);
    }
}
