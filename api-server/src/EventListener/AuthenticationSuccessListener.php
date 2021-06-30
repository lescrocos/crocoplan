<?php


namespace App\EventListener;


use App\Entity\Utilisateur;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{
    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof Utilisateur) {
            return;
        }

        $data['utilisateur'] = $this->normalizer->normalize($user, 'jsonld', ['groups' => 'authentication_success']);

        $event->setData($data);
    }
}