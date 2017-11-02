<?php

namespace Vusalba\VueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InputTable
 *
 * @ORM\Table(name="input_table", indexes={@ORM\Index(name="IDX_4ED747F27F3310E7", columns={"composant_id"}), @ORM\Index(name="IDX_4ED747F2460D9FD7", columns={"node_id"})})
 * @ORM\Entity(repositoryClass="Vusalba\VueBundle\Repository\InputTableRepository")
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
     * @ORM\Column(name="NombreTotal", type="float", precision=10, scale=0, nullable=true)
     */
    private $nombretotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateremise", type="date", nullable=true)
     */
    private $dateremise;

    /**
     * @var string
     *
     * @ORM\Column(name="dateremise_image", type="string", length=255, nullable=true)
     */
    private $dateremiseImage;

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
     * @var \Composant
     *
     * @ORM\ManyToOne(targetEntity="Composant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="composant_id", referencedColumnName="id")
     * })
     */
    private $composant;



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
     * Set dateremise
     *
     * @param \DateTime $dateremise
     *
     * @return InputTable
     */
    public function setDateremise($dateremise)
    {
        $this->dateremise = $dateremise;

        return $this;
    }

    /**
     * Get dateremise
     *
     * @return \DateTime
     */
    public function getDateremise()
    {
        return $this->dateremise;
    }

    /**
     * Set dateremiseImage
     *
     * @param string $dateremiseImage
     *
     * @return InputTable
     */
    public function setDateremiseImage($dateremiseImage)
    {
        $this->dateremiseImage = $dateremiseImage;

        return $this;
    }

    /**
     * Get dateremiseImage
     *
     * @return string
     */
    public function getDateremiseImage()
    {
        return $this->dateremiseImage;
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
    * @return float
    */
   public function getNombretotal()
   {
      return $this->nombretotal;
   }

   /**
    * @param float $nombretotal
    */
   public function setNombretotal($nombretotal)
   {
      $this->nombretotal = $nombretotal;
   }
}
