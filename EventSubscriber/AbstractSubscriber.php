<?php

declare(strict_types = 1);

/*
 * This file is part of the EasyblueDoctrineExtensionsBundle project.
 * (c) Easyblue <support@easyblue.io>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Easyblue\DoctrineExtensionsBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Easyblue\DoctrineExtensionsBundle\Reflection\ClassAnalyzer;

abstract class AbstractSubscriber implements EventSubscriber
{
    /**
     * @var ClassAnalyzer
     */
    protected $classAnalyser;

    /**
     * AbstractSubscriber constructor.
     *
     * @param ClassAnalyzer $classAnalyser
     */
    public function __construct(ClassAnalyzer $classAnalyser)
    {
        $this->classAnalyser = $classAnalyser;
    }
}
