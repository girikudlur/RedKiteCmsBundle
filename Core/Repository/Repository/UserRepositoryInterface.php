<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license infpageRepositoryation, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace RedKiteLabs\RedKiteCmsBundle\Core\Repository\Repository;

/**
 * Defines the methods used to fetch user records
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
interface UserRepositoryInterface
{
    /**
     * Fetches an user record using its primary key
     *
     * @param int       The primary key
     * @return object The fetched object
     */
    public function fromPK($id);

    /**
     * Fetches an user record using its primary key
     *
     * @param int       The primary key
     * @return object The fetched object
     */
    public function fromUserName($userName);

    /**
     * Fetches the site's users
     *
     * @return object The fetched objects
     */
    public function activeUsers();
}
