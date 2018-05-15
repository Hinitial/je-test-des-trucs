<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 13/05/18
 * Time: 20:03
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Booking;
use AppBundle\Service\BookingManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\DateTime;

class BookingManagerTest extends TestCase
{
    protected $session;
    protected $template;
    protected $entity;
    protected $formFactory;
    protected $requestStack;
    protected $router;
    protected $priceTicketManager;
    protected $validation;

    protected $bookingManager;

    public function setUp()
    {
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->template = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entity = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->priceTicketManager = $this->getMockBuilder('\AppBundle\Service\PriceTicketManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validation = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bookingManager = new BookingManager(
            $this->session,
            $this->template,
            $this->entity,
            $this->formFactory,
            $this->requestStack,
            $this->router,
            $this->priceTicketManager,
            $this->validation);
    }

    public function testVerifyStep(){

        $booking = new Booking();
        $date= new \DateTime('2018/06/06');
            $booking
                ->setVisitDate($date)
                ->setEmail('ontest@destrucs.com')
                ->setTicketType(true)
                ->setTicketNumber(1);
        $this->assertSame(true, $this->bookingManager->verifyStep('step_1', $booking));
    }

    /**
     * @dataProvider dataForAddNumber
     * @param $number
     */
    public function testAddNumber($number){
        $booking = new Booking();
        $booking->setTicketNumber($number);
        $this->bookingManager->addTickets($booking);

        $this->assertSame($number, $booking->getTickets()->count());
    }

    public function dataForAddNumber(){
        return[
            [3],
            [7],
            [1],
            [4]
        ];
    }

    public function tearDown()
    {
        $this->session = null;
        $this->template = null;
        $this->entity = null;
        $this->formFactory = null;
        $this->requestStack = null;
        $this->router = null;
        $this->priceTicketManager = null;
        $this->validation = null;

        $this->bookingManager = null;
    }
}