<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
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
     * @Assert\Length(max=255, maxMessage="Code trop long")
     */
    private $codeReservation;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reservation", type="datetime")
     * @Assert\DateTime()
     */
    private $dateReservation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="jour_visite", type="datetime")
     * @Assert\DateTime()
     * @AppAssert\NoPastDate
     * @AppAssert\NoSunday
     * @AppAssert\NoTuesday
     */
    private $jourVisite;

    /**
     * @var string
     *
     * @ORM\Column(name="type_billet", type="string", length=30)
     * @Assert\Length(max=30, maxMessage="Type trop long")
     */
    private $typeBillet;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Billet", mappedBy="reservation")
     */
    private $billets;

    /**
     * @Assert\Type(type="int")
     * @Assert\Range(min="1",max="7")
     */
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->billets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add billet
     *
     * @param \AppBundle\Entity\Billet $billet
     *
     * @return Reservation
     */
    public function addBillet(\AppBundle\Entity\Billet $billet)
    {
        $this->billets[] = $billet;

        $billet->setReservation($this);

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \AppBundle\Entity\Billet $billet
     */
    public function removeBillet(\AppBundle\Entity\Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * @return int Le prix de la reservation
     */
    public function getPrixReservation(){
        $prix = 0;
        foreach ($this->getBillets() as $billet){
            $prix = $prix + $billet->getPrixBillet();
        }
        return $prix;
    }
}
