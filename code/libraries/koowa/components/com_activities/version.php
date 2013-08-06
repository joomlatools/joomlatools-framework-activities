<?php
/**
 * @package     Nooku_Components
 * @subpackage  Activities
 * @copyright	Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

class ComActivitiesVersion extends KObject
{
    const VERSION = '1.0.2';

    /**
     * Get the version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
