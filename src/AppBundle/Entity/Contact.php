<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 27/03/18
 * Time: 13:38
 */

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\Length(min="2", max="50")
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @Assert\Length(min="2", max="50")
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @Assert\Length(min="2", max="50")
     * @Assert\NotBlank()
     */
    private $titre;

    /**
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


}