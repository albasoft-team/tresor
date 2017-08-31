<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InputTable
 *
 * @ORM\Table(name="input_table", indexes={@ORM\Index(name="comp_inputtable_id", columns={"composant_id"}), @ORM\Index(name="node_inputtable_id", columns={"node_id"})})
 * @ORM\Entity
 */
class InputTable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=1000, nullable=true)
     */
    private $tags;

    /**
     * @var float
     *
     * @ORM\Column(name="MontantTotal", type="float", precision=10, scale=0, nullable=true)
     */
    private $montanttotal;

    /**
     * @var float
     *
     * @ORM\Column(name="NbCheques", type="float", precision=10, scale=0, nullable=true)
     */
    private $nbcheques;

    /**
     * @var \Composant
     *
     * @ORM\ManyToOne(targetEntity="Composant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="composant_id", referencedColumnName="id")
     * })
     */
    private $composant;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateremise", type="date", nullable=true)
     */
    private $dateremise;

    /**
     * @var \Node
     *
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     * })
     */
    private $node;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return InputTable
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set montanttotal
     *
     * @param float $montanttotal
     *
     * @return InputTable
     */
    public function setMontanttotal($montanttotal)
    {
        $this->montanttotal = $montanttotal;

        return $this;
    }

    /**
     * Get montanttotal
     *
     * @return float
     */
    public function getMontanttotal()
    {
        return $this->montanttotal;
    }

    /**
     * Set nbcheques
     *
     * @param float $nbcheques
     *
     * @return InputTable
     */
    public function setNbcheques($nbcheques)
    {
        $this->nbcheques = $nbcheques;

        return $this;
    }

    /**
     * Get nbcheques
     *
     * @return float
     */
    public function getNbcheques()
    {
        return $this->nbcheques;
    }

    /**
     * Set composant
     *
     * @param \Vusalba\VueBundle\Entity\Composant $composant
     *
     * @return InputTable
     */
    public function setComposant(\Vusalba\VueBundle\Entity\Composant $composant = null)
    {
        $this->composant = $composant;

        return $this;
    }

    /**
     * Get composant
     *
     * @return \Vusalba\VueBundle\Entity\Composant
     */
    public function getComposant()
    {
        return $this->composant;
    }

    /**
     * Set node
     *
     * @param \Vusalba\VueBundle\Entity\Node $node
     *
     * @return InputTable
     */
    public function setNode(\Vusalba\VueBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Vusalba\VueBundle\Entity\Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return \DateTime
     */
    public function getDateremise()
    {
        return $this->dateremise;
    }

    /**
     * @param \DateTime $dateremise
     */
    public function setDateremise($dateremise)
    {
        $this->dateremise = $dateremise;
    }
}
