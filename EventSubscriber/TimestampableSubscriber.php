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
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Easyblue\DoctrineExtensionsBundle\Reflection\ClassAnalyzer;

/**
 * Class TimestampableSubscriber.
 */
class TimestampableSubscriber extends AbstractSubscriber
{
    /**
     * @var string
     */
    private $timestampableTrait;

    /**
     * @var string
     */
    private $dbFieldType;

    /**
     * TimestampableSubscriber constructor.
     *
     * @param ClassAnalyzer $classAnalyzer
     * @param string        $timestampableTrait
     * @param string        $dbFieldType
     */
    public function __construct(ClassAnalyzer $classAnalyzer, string $timestampableTrait, string $dbFieldType)
    {
        parent::__construct($classAnalyzer);

        $this->timestampableTrait = $timestampableTrait;
        $this->dbFieldType        = $dbFieldType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
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
        if ($this->isTimestampable($classMetadata)) {
            if ($this->classAnalyser->hasMethod($classMetadata->reflClass, 'updateTimestamps')) {
                $classMetadata->addLifecycleCallback('updateTimestamps', Events::prePersist);
                $classMetadata->addLifecycleCallback('updateTimestamps', Events::preUpdate);
            }
            foreach (['createdAt', 'updatedAt'] as $field) {
                if (!$classMetadata->hasField($field)) {
                    $classMetadata->mapField([
                        'fieldName' => $field,
                        'type'      => $this->dbFieldType,
                        'nullable'  => true,
                    ]);
                }
            }
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return bool
     */
    private function isTimestampable(ClassMetadata $classMetadata): bool
    {
        return $this->classAnalyser->hasTrait(
            $classMetadata->reflClass,
            $this->timestampableTrait
        );
    }
}
