<?php

namespace SoureCode\Bundle\Token\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use function get_class;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;
use SoureCode\Bundle\Token\Repository\TokenRepository;

class TokenEventSubscriber implements EventSubscriber
{
    protected TokenRepository $repository;

    protected ObjectManager $manager;

    public function __construct(ObjectManager $manager, TokenRepository $repository)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    public function preRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        if ($entity instanceof TokenAwareInterface) {
            $tokens = $this->repository->findBy(
                [
                    'resourceType' => get_class($entity),
                    'resourceId' => $entity->getObjectIdentifier(),
                ]
            );

            foreach ($tokens as $token) {
                $this->manager->remove($token);
            }

            $this->manager->flush();
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }
}
