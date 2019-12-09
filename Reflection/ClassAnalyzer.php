<?php

declare(strict_types = 1);

namespace Easyblue\DoctrineExtensionsBundle\Reflection;

/**
 * Class ClassAnalyzer
 * @package Easyblue\DoctrineExtensionsBundle
 */
class ClassAnalyzer
{
    /**
     * @param \ReflectionClass $class
     * @param                  $traitName
     *
     * @return bool
     */
    public function hasTrait(\ReflectionClass $class, string $traitName): bool
    {
        if (in_array($traitName, $class->getTraitNames())) {
            return true;
        }
        $parentClass = $class->getParentClass();
        if ((false === $parentClass) || (null === $parentClass)) {
            return false;
        }

        return $this->hasTrait($parentClass, $traitName);
    }

    /**
     * @param \ReflectionClass $class
     * @param                  $methodName
     *
     * @return bool
     */
    public function hasMethod(\ReflectionClass $class, string $methodName): bool
    {
        return $class->hasMethod($methodName);
    }

    /**
     * @param \ReflectionClass $class
     * @param                  $propertyName
     *
     * @return bool
     */
    public function hasProperty(\ReflectionClass $class, string $propertyName): bool
    {
        if ($class->hasProperty($propertyName)) {
            return true;
        }
        $parentClass = $class->getParentClass();
        if (false === $parentClass) {
            return false;
        }

        return $this->hasProperty($parentClass, $propertyName);
    }
}
