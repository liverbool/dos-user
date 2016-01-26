<?php

namespace DoS\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Rbac\Model\Role;
use Sylius\Component\User\Model\User as BaseUser;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Sylius\Component\Media\Model\ImageInterface;

class User extends BaseUser implements UserInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $displayname;

    /**
     * @var string
     */
    protected $confirmationType;

    /**
     * @var \DateTime
     */
    protected $confirmedAt;

    /**
     * @var ArrayCollection|Role[]
     */
    protected $authorizationRoles;

    /**
     * @var ImageInterface
     */
    protected $picture;

    public function __construct()
    {
        parent::__construct();

        $this->authorizationRoles = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationRoles()
    {
        return $this->authorizationRoles;
    }

    /**
     * @param ArrayCollection|\Sylius\Component\Rbac\Model\Role[] $authorizationRoles
     */
    public function setAuthorizationRoles($authorizationRoles)
    {
        if (!$authorizationRoles instanceof Collection) {
            $authorizationRoles = new ArrayCollection($authorizationRoles);
        }

        $this->authorizationRoles = $authorizationRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function resizeSecurityRoles()
    {
        $this->roles = array(self::DEFAULT_ROLE);

        foreach($this->authorizationRoles as $role) {
            foreach($role->getSecurityRoles() as $r) {
                $this->roles[] = $r;
            }
        }
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        $customer = $this->getCustomer();

        return $this->displayname ?:
            (
                $customer && trim($customer->getFullName())
                ? $customer->getFullName()
                : $this->username
            )
        ;
    }

    /**
     * @param null|string $displayname
     */
    public function setDisplayName($displayname = null)
    {
        $this->displayname = $displayname;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string|void
     */
    public function getLang()
    {
        if ($this->locale) {
            if (preg_match('/_([a-z]{2})/i', $this->locale, $match)) {
                return strtolower($match[1]);
            }
        }

        return;
    }

    /**
     * @inheritdoc
     */
    public function getMediaPath()
    {
        return '/user/' . $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setPicture(ImageInterface $picture = null)
    {
        $this->picture = $picture;
    }

    /**
     * {@inheritdoc}
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfilePicture()
    {
        if ($this->picture) {
            return $this->picture->getMediaId();
        }

        foreach ($this->oauthAccounts as $account) {
            if ($avatar = $account->getProfilePicture()) {
                return $avatar;
            }
        }

        return;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * @inheritdoc
     */
    public function setConfirmedAt(\DateTime $confirmedAt = null)
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @inheritdoc
     */
    public function confirmed(\DateTime $confirmedAt = null)
    {
        $this->setConfirmedAt($confirmedAt ?: new \DateTime());
        $this->setEnabled(true);
        $this->setConfirmationToken(null);
        $this->setPasswordRequestedAt(null);
    }

    /**
     * @inheritdoc
     */
    public function isConfirmed()
    {
        return $this->confirmedAt || $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean)$boolean;

        if (!$this->isConfirmed()) {
            $this->enabled = false;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationChannel($propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($this, $propertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationRequestedAt()
    {
        return $this->getPasswordRequestedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationRequestedAt(\DateTime $dateTime = null)
    {
        $this->setPasswordRequestedAt($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationConfirmedAt()
    {
        return $this->getConfirmedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationConfirmedAt(\DateTime $dateTime = null)
    {
        $this->setConfirmedAt($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmationConfirmed()
    {
        return $this->isConfirmed();
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationRequest($token)
    {
        $this->setConfirmationToken($token);
        $this->setConfirmationRequestedAt(new \DateTime());
        $this->setEnabled(false);
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationConfirm()
    {
        $this->confirmed();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationType()
    {
        return $this->confirmationType;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationType($confirmationType)
    {
        $this->confirmationType = $confirmationType;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationDisableAccess()
    {
        $this->enabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationEnableAccess()
    {
        $this->enabled = true;
    }
}
