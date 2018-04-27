<?php

namespace AppBundle\Entity;

use AppBundle\Service\PriceTicketManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;


/**
 * Billet
 *
 * @ORM\Table(name="lvr_billet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BilletRepository")
 */
class Billet
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
     * @ORM\Column(name="nom", type="string", length=50)
     * @Assert\Length(max=50, maxMessage="Nom trop long")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50)
     * @Assert\Length(max=50, maxMessage="Prenom trop long")
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=25)
     * @Assert\Length(max=25)
     */
    private $pays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_naissance", type="datetime")
     * @Assert\DateTime()
     * @AppAssert\NoFutureDate
     */
    private $dateNaissance;

    /**
     * @var bool
     *
     * @ORM\Column(name="tarif_reduit", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $tarifReduit;

    /**
     * @var double
     *
     * @ORM\Column(name="prix", type="decimal", scale=2)
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Reservation", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reservation;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set tarifReduit
     *
     * @param boolean $tarifReduit
     *
     * @return Billet
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * Get tarifReduit
     *
     * @return bool
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * Set reservation
     *
     * @param \AppBundle\Entity\Reservation $reservation
     *
     * @return Billet
     */
    public function setReservation(\AppBundle\Entity\Reservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \AppBundle\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * @return int l'age de la personne
     */
    public function getAge(){
        $now = new \DateTime();
        $intervale = $now->diff($this->getDateNaissance());
        $age = $intervale->format('%Y');
        return (int) ($age);
    }

    /**
     * @return string
     */
    public function getTicketLabel(){
        switch ($this->getPrix()){
            case PriceTicketManager::PRIX_GRATUIT:
                return PriceTicketManager::LABEL_GRATUIT;
            case PriceTicketManager::PRIX_ENFANT:
                return PriceTicketManager::LABEL_ENFANT;
            case PriceTicketManager::PRIX_TARIF_REDUIT:
                return PriceTicketManager::LABEL_TARIF_REDUIT;
            case PriceTicketManager::PRIX_SENIOR:
                return PriceTicketManager::LABEL_SENIOR;
            case PriceTicketManager::PRIX_NORMAL:
                return PriceTicketManager::LABEL_NORMAL;
            default:
                return "Error";
        }
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Billet
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }
}
