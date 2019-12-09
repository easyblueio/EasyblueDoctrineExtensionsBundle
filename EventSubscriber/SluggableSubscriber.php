<?php

declare(strict_types = 1);

/*
 * This file is part of the EasyblueDoctrineExtensionsBundle project.
 * (c) Easyblue <support@easyblue.io>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Easyblue\DoctrineExtensionsBundle\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Easyblue\DoctrineExtensionsBundle\Reflection\ClassAnalyzer;

/**
 * Class SluggableSubscriber.
 */
class SluggableSubscriber extends AbstractSubscriber
{
    /**
     * @var string
     */
    private $sluggableTrait;

    /**
     * SoftDeletableSubscriber constructor.
     *
     * @param ClassAnalyzer $classAnalyzer
     * @param string        $sluggableTrait
     */
    public function __construct(ClassAnalyzer $classAnalyzer, string $sluggableTrait)
    {
        parent::__construct($classAnalyzer);

        $this->sluggableTrait = $sluggableTrait;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     *
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }
        if ($this->isSluggable($classMetadata)) {
            if (!$classMetadata->hasField('slug')) {
                $classMetadata->mapField([
                    'fieldName' => 'slug',
                    'type'      => 'string',
                    'nullable'  => true,
                ]);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity        = $eventArgs->getEntity();
        $em            = $eventArgs->getEntityManager();
        $classMetadata = $em->getClassMetadata(\get_class($entity));
        if ($this->isSluggable($classMetadata)) {
            $entity->generateSlug();
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity        = $eventArgs->getEntity();
        $em            = $eventArgs->getEntityManager();
        $classMetadata = $em->getClassMetadata(\get_class($entity));
        if ($this->isSluggable($classMetadata)) {
            $entity->generateSlug();
        }
    }

    /**
     * @return array|string[]
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata, Events::prePersist, Events::preUpdate];
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return bool
     */
    private function isSluggable(ClassMetadata $classMetadata): bool
    {
        return $this->classAnalyser->hasTrait(
            $classMetadata->reflClass,
            $this->sluggableTrait
        );
    }
}
