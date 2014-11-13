<?php

namespace Demofony2\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProcessParticipation
 * @ORM\Table(name="demofony2_process_participation")
 * @ORM\Entity
 */
class ProcessParticipation extends ParticipationBaseAbstract
{

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $presentationAt;

    /**
     * @var \DateTime
     * @ORM\Column( type="datetime")
     */
    private $debateAt;

    /**
     * @ORM\ManyToMany(targetEntity="Demofony2\AppBundle\Entity\Image")
     * @ORM\JoinTable(name="demofony2_process_participation_images",
     *      joinColumns={@ORM\JoinColumn(name="process_participation_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $images;

    /**
     * @ORM\ManyToMany(targetEntity="Demofony2\AppBundle\Entity\Document")
     * @ORM\JoinTable(name="demofony2_process_participation_documents",
     *      joinColumns={@ORM\JoinColumn(name="process_participation_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $documents;
    /**
     * @ORM\ManyToOne(targetEntity="Demofony2\UserBundle\Entity\User", inversedBy="processParticipations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set presentationAt
     *
     * @param \DateTime $presentationAt
     *
     * @return ProcessParticipation
     */
    public function setPresentationAt($presentationAt)
    {
        $this->presentationAt = $presentationAt;

        return $this;
    }

    /**
     * Get presentationAt
     * @return \DateTime
     */
    public function getPresentationAt()
    {
        return $this->presentationAt;
    }

    /**
     * Set debateAt
     *
     * @param \DateTime $debateAt
     *
     * @return ProcessParticipation
     */
    public function setDebateAt($debateAt)
    {
        $this->debateAt = $debateAt;

        return $this;
    }

    /**
     * Get debateAt
     * @return \DateTime
     */
    public function getDebateAt()
    {
        return $this->debateAt;
    }
}