<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitRepository")
 * @ORM\Table(indexes={@ORM\Index(columns={"visited_at"})})
 */
class Visit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", unique=true, type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="visited_at", type="datetime_immutable", nullable=false)
     */
    private \DateTimeImmutable $visited_at;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private int $status;

    public function __construct(int $status, \DateTimeImmutable $visited_at = null)
    {
        $this->visited_at = ($visited_at != NULL) ? $visited_at : new \DateTimeImmutable("now");
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVisitedAt(): \DateTimeImmutable
    {
        return $this->visited_at;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}