<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enrolls
 *
 * @ORM\Table(name="enrolls", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="course_id", columns={"course_id"})})
 * @ORM\Entity
 */
class Enrolls
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enroll_date", type="datetime", nullable=false)
     */
    private $enrollDate;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Courses
     *
     * @ORM\ManyToOne(targetEntity="Courses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     * })
     */
    private $course;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnrollDate(): ?\DateTimeInterface
    {
        return $this->enrollDate;
    }

    public function setEnrollDate(\DateTimeInterface $enrollDate): self
    {
        $this->enrollDate = $enrollDate;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCourse(): ?Courses
    {
        return $this->course;
    }

    public function setCourse(?Courses $course): self
    {
        $this->course = $course;

        return $this;
    }


}
