<?php

namespace Vusalba\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Vusalba\VueBundle\Entity\Node;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Vusalba\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var Node
     * @ORM\ManyToOne(targetEntity="Vusalba\VueBundle\Entity\Node")
     */
    private $node;
    /**
     * @var Profile
     * @ORM\ManyToMany(targetEntity="Vusalba\UserBundle\Entity\Profile", cascade={"persist"})
     */
    private $profiles;

    public function __construct()
    {
        parent::__construct();
        $this->profiles = new ArrayCollection();
    }

    /**
     * All role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return array_unique(array_merge($this->profiles->toArray(), [new Profile(parent::ROLE_DEFAULT, 'Le role par défaut ')]));
    }

    /**
     * get role by name
     * @param $rolename
     * @return null|Profile
     * @internal param string $role
     */
    public function getRole($rolename) {
        foreach ($this->getRoles() as $profile) {
            if ($rolename == $profile->getRole()) {
                return $profile;
            }
        }
        return null;
    }

    /**
     * @param string $rolename
     * @return bool
     * @internal param string $rolename
     */
    public function hasRole($rolename)
    {
       if ($this->getRole($rolename)) {
           return true;
       }
       return false;
    }

    /**
     * @param Profile $profile
     * @return User
     * @throws \Exception
     * @internal param Profile $profile
     */
    public function addRole($profile)
    {
       if (!$profile instanceof Profile) {
            throw new \Exception('addRole take instance of Profile');
       }
       if ($this->hasRole($profile->getRole())) {
           return $this;
       }
       $this->profiles->add($profile);
       return $this;
    }

    /**
     * @param array $profiles
     * @return mixed
     * @internal param array $roles Of Role objects.
     */
    public function setRoles(array $profiles)
    {
        $this->profiles->clear();
        foreach ($profiles as $profile) {
            $this->addRole($profile);
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this|\FOS\UserBundle\Model\UserInterface|void
     */
    public function removeRole($role)
    {
        $profile = $this->getRole($role);
        if ($profile) {
            $this->profiles->removeElement($profile);
        }
        return $this;
    }
    public function getRolesCollection() {
        return $this->profiles;
    }
    /**
     * Set node
     *
     * @param \Vusalba\VueBundle\Entity\Node $node
     *
     * @return User
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
     * @param Profile $profile
     * @return User
     */
    public function addProfile(\Vusalba\UserBundle\Entity\Profile $profile) {

        $this->profiles[] = $profile;
        return $this;
    }
    public function removeProfile(\Vusalba\UserBundle\Entity\Profile $profile) {
        $this->profiles->removeElement($profile);
    }
    /**
     * @return Profile
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param Profile $profiles
     */
    public function setProfiles($profiles)
    {
        $this->profiles = $profiles;
    }


}
