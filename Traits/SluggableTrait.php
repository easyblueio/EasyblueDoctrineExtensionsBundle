<?php

declare(strict_types = 1);

/*
 * This file is part of the EasyblueDoctrineExtensionsBundle project.
 * (c) Easyblue <support@easyblue.io>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Easyblue\DoctrineExtensionsBundle\Traits;

/**
 * Trait SluggableTrait.
 */
trait SluggableTrait
{
    //<editor-fold desc="Members">
    /**
     * @var string|null
     */
    protected $slug;

    //</editor-fold>

    /**
     * @return array
     */
    abstract public function getSluggableFields(): array;

    public function generateSlug(): void
    {
        if ($this->isRegeneratedSlugOnUpdate() || empty($this->slug)) {
            $fields = $this->getSluggableFields();
            $values = [];
            foreach ($fields as $field) {
                if (property_exists($this, $field)) {
                    $val = $this->{$field};
                } else {
                    $methodName = 'get'.ucfirst($field);
                    if (method_exists($this, $methodName)) {
                        $val = $this->{$methodName}();
                    } else {
                        $val = null;
                    }
                }
                $values[] = $val;
            }
            $this->slug = $this->generateSlugValue($values);
        }
    }

    //<editor-fold desc="Getters">

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    //</editor-fold>

    //<editor-fold desc="Setters">

    /**
     * @param string|null $slug
     *
     * @return $this
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    private function getSlugDelimiter(): string
    {
        return '-';
    }

    /**
     * @return bool
     */
    private function isRegeneratedSlugOnUpdate(): bool
    {
        return true;
    }

    /**
     * @param $values
     *
     * @return string
     */
    private function generateSlugValue($values): string
    {
        $usableValues = [];
        foreach ($values as $fieldValue) {
            if (!empty($fieldValue)) {
                $usableValues[] = $fieldValue;
            }
        }
        if (\count($usableValues) < 1) {
            throw new \UnexpectedValueException(
                'Sluggable expects to have at least one usable (non-empty) field from the following: [ '.implode(',', array_keys($values)).' ]'
            );
        }
        // generate the slug itself
        $sluggableText  = implode(' ', $usableValues);
        $transliterator = \Transliterator::create('sluggable');
        $sluggableText  = $transliterator->transliterate($sluggableText, $this->getSlugDelimiter());
        $urlized        = strtolower(trim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $sluggableText), $this->getSlugDelimiter()));
        $urlized        = preg_replace("/[\/_|+ -]+/", $this->getSlugDelimiter(), $urlized);

        return $urlized;
    }

    //</editor-fold>
}
