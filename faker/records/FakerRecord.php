<?php
/**
 * Faker plugin for Craft CMS
 *
 * Faker Record
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Faker
 * @since     1.0.0
 */

namespace Craft;

class FakerRecord extends BaseRecord
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'faker';
    }

    /**
     * @access protected
     * @return array
     */
   protected function defineAttributes()
    {
        return array(
            'someField'     => array(AttributeType::String, 'default' => ''),
        );
    }

    /**
     * @return array
     */
    public function defineRelations()
    {
        return array(
        );
    }
}