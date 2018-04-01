<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservation
 *
 * @ORM\Table(name="lvr_reservation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_reservation", type="string", length=255)
     */
    private $codeReservation;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reservation", type="datetime")
     */
    private $dateReservation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="jour_visite", type="datetime")
     */
    private $jourVisite;

    /**
     * @var string
     *
     * @ORM\Column(name="type_billet", type="string", length=30)
     */
    private $typeBillet;

    private $nbreBillet;

    /**
     * @return mixed
     */
    public function getNbreBillet()
    {
        return $this->nbreBillet;
    }

    /**
     * @param mixed $nbreBillet
     */
    public function setNbreBillet($nbreBillet)
    {
        $this->nbreBillet = $nbreBillet;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codeReservation
     *
     * @param string $codeReservation
     *
     * @return Reservation
     */
    public function setCodeReservation($codeReservation)
    {
        $this->codeReservation = $codeReservation;

        return $this;
    }

    /**
     * Get codeReservation
     *
     * @return string
     */
    public function getCodeReservation()
    {
        return $this->codeReservation;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Reservation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateReservation
     *
     * @param \DateTime $dateReservation
     *
     * @return Reservation
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set jourVisite
     *
     * @param \DateTime $jourVisite
     *
     * @return Reservation
     */
    public function setJourVisite($jourVisite)
    {
        $this->jourVisite = $jourVisite;

        return $this;
    }

    /**
     * Get jourVisite
     *
     * @return \DateTime
     */
    public function getJourVisite()
    {
        return $this->jourVisite;
    }

    /**
     * Set typeBillet
     *
     * @param string $typeBillet
     *
     * @return Reservation
     */
    public function setTypeBillet($typeBillet)
    {
        $this->typeBillet = $typeBillet;

        return $this;
    }

    /**
     * Get typeBillet
     *
     * @return string
     */
    public function getTypeBillet()
    {
        return $this->typeBillet;
    }
}

