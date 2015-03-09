<?php
/**
 * Demofony2
 *
 * @author: Marc Morales Valldepérez <marcmorales83@gmail.com>
 *
 * Date: 13/11/14
 * Time: 16:52
 */
namespace Demofony2\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Demofony2\AppBundle\Entity\Traits\ImageTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Image
 *
 * @ORM\Table(name="demofony2_image")
 * @ORM\Entity
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="removedAt")
 */
class Image extends BaseAbstract
{
    use ImageTrait;

    /**
     * @Assert\File(
     *     maxSize="500k",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage = "constraint.mime_type"
     * )
     * @Vich\UploadableField(
     *     mapping="participation_image",
     *     fileNameProperty="imageName"
     * )
     * @var File $image
     */
    protected $image;

    /**
     * @var string
     * @Serializer\Groups({"detail"})
     */
    protected $small;

    /**
     * @var string
     * @Serializer\Groups({"detail"})
     */
    protected $big;

    /**
     * @var string
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     * @Serializer\Groups({"detail"})
     */
    protected $alt;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Serializer\Groups({"detail"})
     */
    protected $title;

    /**
     * @var integer
     * @ORM\Column(name="position", type="integer")
     */
    protected $position=1;

    /**
     * @param $url
     *
     * @return $this
     */
    public function setSmall($url)
    {
        $this->small = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSmall()
    {
        return $this->small;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setBig($url)
    {
        $this->big = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getBig()
    {
        return $this->big;
    }

    /**
     * @param $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param $title
     *
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param $position
     *
     * @return Image
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
