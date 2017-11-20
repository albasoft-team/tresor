<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table(name="node")
 * @ORM\Entity(repositoryClass="Vusalba\VueBundle\Repository\NodeRepository")
 */
class Node
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     *
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
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var Node
     * @ORM\ManyToOne(targetEntity="Vusalba\VueBundle\Entity\Node")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var bool
     * @ORM\Column(name="isScopeAnalyse", type="boolean")
     */
    private $isScopeAnalyse;

    /**
     * Get id
     *
     * @return string
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
     * @return Node
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
     * @return Node
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
     * Set level
     *
     * @param integer $level
     *
     * @return Node
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set parent
     *
     * @param \Vusalba\VueBundle\Entity\Node $parent
     *
     * @return Node
     */
    public function setParent(\Vusalba\VueBundle\Entity\Node $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Vusalba\VueBundle\Entity\Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function getIsScopeAnalyse()
    {
        return $this->isScopeAnalyse;
    }

    /**
     * @param bool $isScopeAnalysis
     */
    public function setIsScopeAnalyse($isScopeAnalyse)
    {
        $this->isScopeAnalyse = $isScopeAnalyse;
    }
}
