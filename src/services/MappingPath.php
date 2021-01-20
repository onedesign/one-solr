<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolr\services;

use onedesign\onesolr\OneSolr;

use Craft;
use craft\base\Component;

/**
 * MappingPath Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 */
class MappingPath extends Component
{
    // Public Methods
    // =========================================================================

    public function getMappingTemplates() {
        $path = CRAFT_BASE_PATH . '/templates/onesolr';
        $files = scandir($path);
        foreach ($files as $key=>$file)
        {
            $fileParts = pathinfo($file);
            if($fileParts['extension'] !== 'json')
            {
                unset($files[$key]);
            }
        }

        return $files;
    }
}
