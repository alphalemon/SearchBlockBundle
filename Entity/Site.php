<?php
 
namespace AlphaLemon\Block\SearchBlockBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
 
/**
 * @ORM\Entity
 * @ORM\Table(name="al_site")
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
 
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    private $name;
 
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     * @Assert\MaxLength(255)
     */
    private $url;
 
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\MaxLength(255)
     */
    private $keywords;
 
    public function getId()
    {
        return $this->id;
    }
 
    public function setName($name)
    {
        $this->name = $name;
    }
 
    public function getName()
    {
        return $this->name;
    }
 
    public function setUrl($url)
    {
        $this->url = $url;
    }
 
    public function getUrl()
    {
        return $this->url;
    }
 
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }
 
    public function getKeywords()
    {
        return $this->keywords;
    }
 
}