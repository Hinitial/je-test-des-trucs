<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
/**
 * Booking
 *
 * @ORM\Table(name="lvr_booking")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookingRepository")
 * @AppAssert\NoAllDay()
 * @AppAssert\NotOverThousand()
 */
class Booking
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
     * @ORM\Column(name="booking_code", type="string", length=255)
     * @Assert\NotBlank(groups={"step_3"})
     * @Assert\Length(max=255, maxMessage="Code trop long", groups={"step_3"})
     */
    private $bookingCode;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank(groups={"step_1"})
     * @Assert\Email(groups={"step_1"})
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="booking_date", type="datetime")
     * @Assert\NotBlank(groups={"step_3"})
     * @Assert\DateTime(groups={"step_3"})
     */
    private $bookingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visit_date", type="datetime")
     * @Assert\NotBlank(groups={"step_1"})
     * @Assert\DateTime(groups={"step_1"})
     * @AppAssert\NoPastDate
     * @AppAssert\NoSunday
     * @AppAssert\NoTuesday
     * @AppAssert\TooFarFuture
     */
    private $visitDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="ticket_type", type="boolean")
     * @Assert\NotBlank(groups={"step_1"})
     * @Assert\Type(type="bool", groups={"step_1"})
     */
    private $ticketType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ticket", mappedBy="booking")
     * @Assert\Valid(groups={"step_2"})
     */
    private $tickets;

    /**
     * @Assert\NotBlank(groups={"step_1"})
     * @Assert\Type(type="int", groups={"step_1"})
     * @Assert\Range(min="1",max="7", minMessage="Vous devez au moins acheter {{ limit }} billet.", maxMessage="Vous ne pouvez pas acheter plus de {{ limit }} billets en une seul fois.")
     */
    private $ticketNumber;

    /**
     * @return mixed
     */
    public function getTicketNumber()
    {
        return $this->ticketNumber;
    }

    /**
     * @param mixed $ticketNumber
     */
    public function setTicketNumber($ticketNumber)
    {
        $this->ticketNumber = $ticketNumber;
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
     * @param string $bookingCode
     *
     * @return Booking
     */
    public function setBookingCode($bookingCode)
    {
        $this->bookingCode = $bookingCode;

        return $this;
    }

    /**
     * Get codeReservation
     *
     * @return string
     */
    public function getBookingCode()
    {
        return $this->bookingCode;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Booking
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
     * @param \DateTime $bookingDate
     *
     * @return Booking
     */
    public function setBookingDate($bookingDate)
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * Set jourVisite
     *
     * @param \DateTime $visitDate
     *
     * @return Booking
     */
    public function setVisitDate($visitDate)
    {
        $this->visitDate = $visitDate;

        return $this;
    }

    /**
     * Get jourVisite
     *
     * @return \DateTime
     */
    public function getVisitDate()
    {
        return $this->visitDate;
    }

    /**
     * Set typeBillet
     *
     * @param string $ticketType
     *
     * @return Booking
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get typeBillet
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add ticket
     *
     * @param \AppBundle\Entity\Ticket $ticket
     *
     * @return Booking
     */
    public function addTicket(\AppBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        $ticket->setBooking($this);

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \AppBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\AppBundle\Entity\Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get ticket
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @return int Le prix de la reservation
     */
    public function getBookingPrice(){
        $price = 0;
        foreach ($this->getTickets() as $ticket){
            $price = $price + $ticket->getPrice();
        }
        return $price;
    }
}
