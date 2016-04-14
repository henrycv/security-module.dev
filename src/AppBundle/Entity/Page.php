<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPage;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_parent_id", type="integer", nullable=true)
     */
    private $pageParentId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="route_name", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $routeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="integer", nullable=false)
     */
    private $visible;

    /**
     * @var array
     */
    protected $actions;


    public function __construct()
    {
        $this->actions = array();
    }

    /**
     * Get idPage
     *
     * @return integer
     */
    public function getIdPage()
    {
        return $this->idPage;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Page
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
     * Set pageParentId
     *
     * @param integer $pageParentId
     * @return Page
     */
    public function setPageParentId($pageParentId)
    {
        $this->pageParentId = $pageParentId;

        return $this;
    }

    /**
     * Get pageParentId
     *
     * @return integer
     */
    public function getPageParentId()
    {
        return $this->pageParentId;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set routeName
     *
     * @param string $routeName
     * @return Page
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Set visible
     *
     * @param integer $visible
     * @return Page
     */
    public function setVisible($visible)
    {
        $this->visible = ($visible ? 1 : 0);

        return $this;
    }

    /**
     * Get visible
     *
     * @return integer
     */
    public function getVisible()
    {
        return ((integer) $this->visible === 1);
    }

    /**
     * Returns the page actions
     *
     * @return array The Actions
     */
    public function getActions()
    {
        return $this->actions;
    }

    public function setActions($actions)
    {
        $this->actions = array();

        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    public function addAction($action)
    {
        $this->actions[] = $action;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
