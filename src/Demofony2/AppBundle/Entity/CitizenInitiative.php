<?php
namespace Demofony2\AppBundle\Entity;

use Demofony2\AppBundle\Enum\CitizenInitiativeStateEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CitizenInitiative
 *
 * @ORM\Table(name="demofony2_citizen_initiative")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="removedAt")
 */
class CitizenInitiative extends BaseAbstract
{

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_at", type="datetime")
     * @Assert\NotBlank()
     */
    private $startAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="finish_at", type="datetime")
     * @Assert\NotBlank()
     */
    private $finishAt;

    /**
     * @var string
     * @ORM\Column(name="person", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $person;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Image", mappedBy="citizenInitiative", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     **/
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="Demofony2\AppBundle\Entity\Document", mappedBy="citizenInitiative", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     **/
    protected $documents;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return CitizenInitiative
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return CitizenInitiative
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param \DateTime $startAt
     *
     * @return CitizenInitiative
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinishAt()
    {
        return $this->finishAt;
    }

    /**
     * @param \DateTime $finishAt
     *
     * @return CitizenInitiative
     */
    public function setFinishAt($finishAt)
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $person
     *
     * @return CitizenInitiative
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     *
     * @return CitizenInitiative
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }


    /**
     * Add Images
     *
     * @param  Image                $image
     * @return CitizenInitiative
     */
    public function addImage(Image $image)
    {
        $image->setCitizenInitiative($this);
        $this->images[] = $image;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $documents
     *
     * @return CitizenInitiative
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Add Documents
     *
     * @param  Document                  $document
     * @return CitizenInitiative
     */
    public function addDocument(Document $document)
    {
        $document->setCitizenInitiative($this);
        $this->documents[] = $document;

        return $this;
    }

    public function getState()
    {
        $now = new \DateTime();

        if ($now < $this->getStartAt()) {
            return CitizenInitiativeStateEnum::DRAFT;
        }

        if ($now > $this->getStartAt() && $now < $this->getFinishAt()) {
            return CitizenInitiativeStateEnum::OPEN;
        }
        if ($now > $this->getStartAt() && $now > $this->getFinishAt()) {
            return CitizenInitiativeStateEnum::CLOSED;
        }

        return CitizenInitiativeStateEnum::DRAFT;
    }

}