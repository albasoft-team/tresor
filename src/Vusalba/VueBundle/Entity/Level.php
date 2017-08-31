<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Level
 *
 * @ORM\Table(name="level")
 * @ORM\Entity(repositoryClass="Vusalba\VueBundle\Repository\LevelRepository")
 */
class Level
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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;
    /**
     * @var int
     * @ORM\Column(name="niveau", type="integer")
     */
    private $niveau = 0;
    /**
     * @var bool
     *
     * @ORM\Column(name="scope_analysis", type="boolean")
     */
    private $scopeAnalysis=false;


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
     * @return Level
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
     * @return Level
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
     * Set scopeAnalysis
     *
     * @param boolean $scopeAnalysis
     *
     * @return Level
     */
    public function setScopeAnalysis($scopeAnalysis)
    {
        $this->scopeAnalysis = $scopeAnalysis;

        return $this;
    }

    /**
     * Get scopeAnalysis
     *
     * @return bool
     */
    public function getScopeAnalysis()
    {
        return $this->scopeAnalysis;
    }

    /**
     * @return int
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param int $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }
}

