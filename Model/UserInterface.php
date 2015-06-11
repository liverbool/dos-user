<?php

namespace DoS\UserBundle\Model;

use DoS\ResourceBundle\Model\ImageInterface;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * User interface.
 */
interface UserInterface extends BaseUserInterface, ImageInterface, ConfirmationSubjectInterface
{
    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param $displayname
     */
    public function setDisplayName($displayname);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return null|string
     */
    public function getProfilePicture();

    /**
     * @param \DateTime|null $confirmedAt
     *
     * @return mixed
     */
    public function confirmed(\DateTime $confirmedAt = null);

    /**
     * @return bool
     */
    public function isConfirmed();

    /**
     * @return \DateTime
     */
    public function getConfirmedAt();

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(\DateTime $confirmedAt = null);
}
