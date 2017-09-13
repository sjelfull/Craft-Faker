<?php
/**
 * Faker plugin for Craft CMS
 *
 * Faker your data, baby
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Faker
 * @since     1.0.0
 */

namespace Craft;

class FakerPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init ()
    {
        parent::init();
        require_once(CRAFT_PLUGINS_PATH . 'faker/vendor/autoload.php');
    }

    /**
     * @return mixed
     */
    public function getName ()
    {
        return Craft::t('Faker');
    }

    /**
     * @return mixed
     */
    public function getDescription ()
    {
        return Craft::t('Fake your data, baby');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl ()
    {
        return 'https://github.com/sjelfull/Craft-Faker/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl ()
    {
        return 'https://raw.githubusercontent.com/sjelfull/Craft-Faker/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion ()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion ()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper ()
    {
        return 'Fred Carlsen';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl ()
    {
        return 'http://sjelfull.no';
    }
}