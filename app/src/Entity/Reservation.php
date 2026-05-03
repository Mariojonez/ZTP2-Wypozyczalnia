<?php
/**
 * Reservation entity.
 */

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Reservation.
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * User.
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[Assert\Type(User::class)]
    #[Assert\NotBlank]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Task.
     */
    #[ORM\ManyToOne(targetEntity: Task::class, fetch: 'EXTRA_LAZY')]
    #[Assert\Type(Task::class)]
    #[Assert\NotBlank]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $comment = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $status = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Setter for Id.
     *
     * @param int|null $id Id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Getter for user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User|null $user User
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Getter for task.
     *
     * @return Task|null Task
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * Setter for task.
     *
     * @param Task|null $task Task
     */
    public function setTask(?Task $task): void
    {
        $this->task = $task;
    }

    /**
     * Getter for comment.
     *
     * @return string|null comment
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Setter for comment.
     *
     * @param string|null $comment comment
     *
     * @return static
     */
    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Getter for status.
     *
     * @return string|null Status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Setter for status.
     *
     * @param string $status Status
     *
     * @return static
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
