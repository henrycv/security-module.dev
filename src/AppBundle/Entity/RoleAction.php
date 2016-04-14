<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleAction
 *
 * @ORM\Table(name="role_action")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleActionRepository")
 */
class RoleAction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_role_action", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRoleAction;

    /**
     * @var integer
     *
     * @ORM\Column(name="role_id", type="integer", nullable=false)
     */
    private $roleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId;



    /**
     * Get idRoleAction
     *
     * @return integer 
     */
    public function getIdRoleAction()
    {
        return $this->idRoleAction;
    }

    /**
     * Set roleId
     *
     * @param integer $roleId
     * @return RoleAction
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get roleId
     *
     * @return integer 
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     * @return RoleAction
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
