<?php

declare(strict_types = 1);

/*
 * This file is part of the EasyblueDoctrineExtensionsBundle project.
 * (c) Easyblue <support@easyblue.io>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Easyblue\DoctrineExtensionsBundle\EventSubscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\ORMException;
use Easyblue\DoctrineExtensionsBundle\Reflection\ClassAnalyzer;

/**
 * Class SoftDeletableSubscriber.
 */
class SoftDeletableSubscriber extends AbstractSubscriber
{
    /**
     * @var string
     */
    private $softDeletableTrait;

    /**
     * SoftDeletableSubscriber constructor.
     *
     * @param ClassAnalyzer $classAnalyzer
     * @param string        $softDeletableTrait
     */
    public function __construct(ClassAnalyzer $classAnalyzer, string $softDeletableTrait)
    {
        parent::__construct($classAnalyzer);

        $this->softDeletableTrait = $softDeletableTrait;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     *
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $classMetadata = $em->getClassMetadata(\get_class($entity));
            if ($this->isSoftDeletable($classMetadata)) {
                $oldValue = $entity->getDeletedAt();
                $entity->delete();
                $em->persist($entity);
                $uow->propertyChanged($entity, 'deletedAt', $oldValue, $entity->getDeletedAt());
                $uow->scheduleExtraUpdate($entity, [
                    'deletedAt' => [$oldValue, $entity->getDeletedAt()],
                ]);
            }
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     *
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }
        if ($this->isSoftDeletable($classMetadata)) {
            if (!$classMetadata->hasField('deletedAt')) {
                $classMetadata->mapField([
                    'fieldName' => 'deletedAt',
                    'type'      => 'datetime',
                    'nullable'  => true,
                ]);
            }
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return bool
     */
    private function isSoftDeletable(ClassMetadata $classMetadata): bool
    {
        return $this->classAnalyser->hasTrait($classMetadata->reflClass, $this->softDeletableTrait);
    }
}
