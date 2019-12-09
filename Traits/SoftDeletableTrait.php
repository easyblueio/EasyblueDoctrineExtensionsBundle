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
 * Trait SoftDeletableTrait.
 */
trait SoftDeletableTrait
{
    //<editor-fold desc="Members">
    /**
     * @var \DateTimeInterface|null
     */
    protected $deletedAt;

    //</editor-fold>
    public function delete(): void
    {
        $this->deletedAt = $this->currentDateTime();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        if (null !== $this->deletedAt) {
            return $this->deletedAt <= $this->currentDateTime();
        }

        return false;
    }

    /**
     * @param \DateTimeInterface|null $willBeDeletedAt
     *
     * @return bool
     */
    public function willBeDeleted(\DateTimeInterface $willBeDeletedAt = null): bool
    {
        if (null === $this->deletedAt) {
            return false;
        }
        if (null === $willBeDeletedAt) {
            return true;
        }

        return $this->deletedAt <= $willBeDeletedAt;
    }

    //<editor-fold desc="Getters">

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    //</editor-fold>

    //<editor-fold desc="Setters">

    /**
     * @param \DateTimeInterface|null $deletedAt
     *
     * @return self
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    private function currentDateTime(): \DateTimeInterface
    {
        $dateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $dateTime;
    }

    //</editor-fold>
}
