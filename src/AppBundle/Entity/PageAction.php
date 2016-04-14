<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageAction
 *
 * @ORM\Table(name="page_action")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageActionRepository")
 */
class PageAction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_age_action", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAgeAction;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_id", type="integer", nullable=false)
     */
    private $pageId;

    /**
     * @var integer
     *
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId;



    /**
     * Get idAgeAction
     *
     * @return integer
     */
    public function getIdAgeAction()
    {
        return $this->idAgeAction;
    }

    /**
     * Set pageId
     *
     * @param integer $pageId
     * @return PageAction
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     * @return PageAction
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * Get actionId
     *
     * @return integer
     */
    public function getActionId()
    {
        return $this->actionId;
    }
}
