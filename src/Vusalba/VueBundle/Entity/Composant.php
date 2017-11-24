<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Composant
 *
 * @ORM\Table(name="composant")
 * @ORM\Entity(repositoryClass="Vusalba\VueBundle\Repository\ComposantRepository")
 */
class Composant
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var Groupe
     * @ORM\ManyToOne(targetEntity="Vusalba\VueBundle\Entity\Groupe", inversedBy="composants")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id")
     */
    private $groupe;

   /**
    * @var string
    *
    * @ORM\Column(name="lc", type="string", length=255, nullable=true)
    */
    private $lc;

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
     * Set name
     *
     * @param string $name
     *
     * @return Composant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Composant
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set groupe
     *
     * @param \Vusalba\VueBundle\Entity\Groupe $groupe
     *
     * @return Composant
     */
    public function setGroupe(\Vusalba\VueBundle\Entity\Groupe $groupe = null)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return \Vusalba\VueBundle\Entity\Groupe
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

   /**
    * @return string
    */
   public function getLc()
   {
      return $this->lc;
   }

   /**
    * @param string $lc
    */
   public function setLc($lc)
   {
      $this->lc = $lc;
   }
}
