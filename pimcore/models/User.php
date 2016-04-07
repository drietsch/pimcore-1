<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in 
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    User
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Model;

use Pimcore\File;

class User extends User\UserRole
{

    /**
     * @var string
     */
    public $type = "user";

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $firstname;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $language = "en";

    /**
     * @var boolean
     */
    public $admin = false;

    /**
     * @var boolean
     */
    public $active = true;

    /**
     * @var array
     */
    public $roles = array();

    /**
     * @var bool
     */
    public $welcomescreen = false;

    /**
     * @var bool
     */
    public $closeWarning = true;


    /**
     * @var bool
     */
    public $memorizeTabs = true;

    /**
     * @var string|null
     */
    public $apiKey;

    /**
     * @var string|null
     */
    public $contentLanguages;

    /**
     * @var string|null
     */
    public $activePerspective;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        if (strlen($password) > 4) {
            $this->password = $password;
        }
        return $this;
    }

    /**
     * Alias for getName()
     * @deprecated
     * @return string
     */
    public function getUsername()
    {
        return $this->getName();
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->setName($username);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        if ($language) {
            $this->language = $language;
        }
        return $this;
    }

    /**
     * @see getAdmin()
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getAdmin();
    }

    /**
     * @return boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param boolean $admin
     * @return void
     */
    public function setAdmin($admin)
    {
        $this->admin = (bool) $admin;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return void
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * @param String $key
     * @return boolean
     */
    public function isAllowed($key, $type = "permission")
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($type == "permission") {
            if (!$this->getPermission($key)) {
                // check roles
                foreach ($this->getRoles() as $roleId) {
                    $role = User\Role::getById($roleId);
                    if ($role->getPermission($key)) {
                        return true;
                    }
                }
            }

            return $this->getPermission($key);
        } elseif ($type == "class") {
            $classes = $this->getClasses();
            foreach ($this->getRoles() as $roleId) {
                $role = User\Role::getById($roleId);
                $classes = array_merge($classes, $role->getClasses());
            }

            if (!empty($classes)) {
                return in_array($key, $classes);
            } else {
                return true;
            }
        } elseif ($type == "docType") {
            $docTypes = $this->getDocTypes();
            foreach ($this->getRoles() as $roleId) {
                $role = User\Role::getById($roleId);
                $docTypes = array_merge($docTypes, $role->getDocTypes());
            }

            if (!empty($docTypes)) {
                return in_array($key, $docTypes);
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param string $permissionName
     * @return array
     */
    public function getPermission($permissionName)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return parent::getPermission($permissionName);
    }

    /**
     * @param $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        if (is_string($roles) && !empty($roles)) {
            $this->roles = explode(",", $roles);
        } elseif (is_array($roles)) {
            $this->roles = $roles;
        } elseif (empty($roles)) {
            $this->roles = array();
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        if (empty($this->roles)) {
            return array();
        }
        return $this->roles;
    }

    /**
     * @param $welcomescreen
     * @return $this
     */
    public function setWelcomescreen($welcomescreen)
    {
        $this->welcomescreen = (bool) $welcomescreen;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getWelcomescreen()
    {
        return $this->welcomescreen;
    }

    /**
     * @param $closeWarning
     * @return $this
     */
    public function setCloseWarning($closeWarning)
    {
        $this->closeWarning = (bool) $closeWarning;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCloseWarning()
    {
        return $this->closeWarning;
    }

    /**
     * @param $memorizeTabs
     * @return $this
     */
    public function setMemorizeTabs($memorizeTabs)
    {
        $this->memorizeTabs = (bool) $memorizeTabs;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMemorizeTabs()
    {
        return $this->memorizeTabs;
    }

    /**
     * @param $apiKey
     * @throws \Exception
     */
    public function setApiKey($apiKey)
    {
        if (!empty($apiKey) && strlen($apiKey) < 32) {
            throw new \Exception("API-Key has to be at least 32 characters long");
        }
        $this->apiKey = $apiKey;
    }

    /**
     * @return null|string
     */
    public function getApiKey()
    {
        if (empty($this->apiKey)) {
            return null;
        }
        return $this->apiKey;
    }

    /**
     * @param $path
     */
    public function setImage($path)
    {
        $userImageDir = PIMCORE_WEBSITE_VAR . "/user-image";
        if (!is_dir($userImageDir)) {
            File::mkdir($userImageDir);
        }

        $destFile = $userImageDir . "/user-" . $this->getId() . ".png";
        $thumb = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/user-thumbnail-" . $this->getId() . ".png";
        @unlink($destFile);
        @unlink($thumb);
        copy($path, $destFile);
        @chmod($destFile, File::getDefaultMode());
    }

    /**
     * @return string
     */
    public function getImage($width = null, $height = null)
    {
        if (!$width) {
            $width = 46;
        }
        if (!$height) {
            $height = 46;
        }

        $id = $this->getId();
        $user = PIMCORE_WEBSITE_VAR . "/user-image/user-" . $id . ".png";
        if (file_exists($user)) {
            $thumb = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/user-thumbnail-" . $id . ".png";
            if (!file_exists($thumb)) {
                $image = \Pimcore\Image::getInstance();
                $image->load($user);
                $image->cover($width, $height);
                $image->save($thumb, "png");
            }

            return $thumb;
        }

        return PIMCORE_PATH . "/static/img/avatar.png";
    }

    /**
     * @return null|string
     */
    public function getContentLanguages()
    {
        if (strlen($this->contentLanguages)) {
            return explode(',', $this->contentLanguages);
        }
        return array();
    }

    /**
     * @param null|string $contentLanguages
     */
    public function setContentLanguages($contentLanguages)
    {
        if ($contentLanguages && is_array($contentLanguages)) {
            $contentLanguages = implode(',', $contentLanguages);
        }
        $this->contentLanguages = $contentLanguages;
    }

    /**
     * @return null|string
     */
    public function getActivePerspective()
    {
        return $this->activePerspective;
    }

    /**
     * @param null|string $activePerspective
     */
    public function setActivePerspective($activePerspective)
    {
        $this->activePerspective = $activePerspective;
    }
}
