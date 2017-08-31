<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Axis
 *
 * @ORM\Table(name="axis")
 * @ORM\Entity(repositoryClass="Vusalba\VueBundle\Repository\AxisRepository")
 */
class Axis
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;


    /**
     * @var AxeGroupe
     * @ORM\ManyToOne(targetEntity="Vusalba\VueBundle\Entity\AxeGroupe")
     * @ORM\JoinColumn(name="axegroupe_id", referencedColumnName="id")
     */
    private $groupe;

    /**
     * @var bool
     *
     * @ORM\Column(name="iscalculated", type="boolean")
     */
    private $iscalculated;

    /**
     * @var string
     *
     * @ORM\Column(name="formula", type="string", length=255, nullable=true)
     */
    private $formula;

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
     * @return Axis
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
     * @return Axis
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
     * Set iscalculated
     *
     * @param boolean $iscalculated
     *
     * @return Axis
     */
    public function setIscalculated($iscalculated)
    {
        $this->iscalculated = $iscalculated;

        return $this;
    }

    /**
     * Get iscalculated
     *
     * @return bool
     */
    public function getIscalculated()
    {
        return $this->iscalculated;
    }

    /**
     * Set formula
     *
     * @param string $formula
     *
     * @return Axis
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Set groupe
     *
     * @param \Vusalba\VueBundle\Entity\AxeGroupe $groupe
     *
     * @return Axis
     */
    public function setGroupe(\Vusalba\VueBundle\Entity\AxeGroupe $groupe = null)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return \Vusalba\VueBundle\Entity\AxeGroupe
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}
