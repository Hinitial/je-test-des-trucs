<?php

namespace AppBundle\Entity;

use AppBundle\Service\PriceTicketManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;


/**
 * Billet
 *
 * @ORM\Table(name="lvr_tickey")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\Length(max=50, maxMessage="Nom trop long")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50)
     * @Assert\Length(max=50, maxMessage="Prenom trop long")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=25)
     * @Assert\Length(max=25)
     */
    private $country;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime")
     * @Assert\DateTime()
     * @AppAssert\NoFutureDate
     */
    private $birthDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="reduc_price", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $reducPrice;

    /**
     * @var double
     *
     * @ORM\Column(name="price", type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Booking", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;


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
     * @param string $name
     *
     * @return Ticket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set prenom
     *
     * @param string $firstName
     *
     * @return Ticket
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set pays
     *
     * @param string $country
     *
     * @return Ticket
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $birthDate
     *
     * @return Ticket
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set tarifReduit
     *
     * @param boolean $reducPrice
     *
     * @return Ticket
     */
    public function setReducPrice($reducPrice)
    {
        $this->reducPrice = $reducPrice;

        return $this;
    }

    /**
     * Get tarifReduit
     *
     * @return bool
     */
    public function getReducPrice()
    {
        return $this->reducPrice;
    }

    /**
     * Set reservation
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Ticket
     */
    public function setBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \AppBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * @return int l'age de la personne
     */
    public function getAge(){
        $now = new \DateTime();
        $intervale = $now->diff($this->getBirthDate());
        $age = $intervale->format('%Y');
        return (int) ($age);
    }

    /**
     * @return string
     */
    public function getTicketLabel(){
        switch ($this->getPrice()){
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
     * @param string $price
     *
     * @return Ticket
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
}
