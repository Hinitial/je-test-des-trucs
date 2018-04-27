<?php

namespace AppBundle\Entity;

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
    const PRIX_GRATUIT = 0;
    const PRIX_NORMAL = 16;
    const PRIX_SENIOR = 12;
    const PRIX_TARIF_REDUIT = 10;
    const PRIX_ENFANT = 8;

    const AGE_GRATUIT_MAX = 4;
    const AGE_ENFANT_MAX = 12;
    const AGE_SENIOR_MIN = 60;

    const LABEL_GRATUIT = 'Gratuit';
    const LABEL_NORMAL = 'Normal';
    const LABEL_SENIOR = 'Sénior';
    const LABEL_TARIF_REDUIT = 'Tarif réduit';
    const LABEL_ENFANT = 'Enfant';

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
     * @return int Le prix du billet
     */
    public function getPrixBillet(){
        $age = $this->getAge();
        if ($age <= self::AGE_GRATUIT_MAX){
            return self::PRIX_GRATUIT;
        }
        elseif ($age <= self::AGE_ENFANT_MAX){
            return self::PRIX_ENFANT;
        }
        elseif ($this->getTarifReduit()){
            return self::PRIX_TARIF_REDUIT;
        }
        elseif ($age >= self::AGE_SENIOR_MIN){
            return self::PRIX_SENIOR;
        }
        else{
            return self::PRIX_NORMAL;
        }
    }

    /**
     * @return string
     */
    public function getNomPromotion(){
        switch ($this->getPrixBillet()){
            case self::PRIX_GRATUIT:
                return self::LABEL_GRATUIT;
            case self::PRIX_ENFANT:
                return self::LABEL_ENFANT;
            case self::PRIX_TARIF_REDUIT:
                return self::LABEL_TARIF_REDUIT;
            case self::PRIX_SENIOR:
                return self::LABEL_SENIOR;
            case self::PRIX_NORMAL:
                return self::LABEL_NORMAL;
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
