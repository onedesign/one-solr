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
use onedesign\onesolr\models\MappingPathRecord;
use phpDocumentor\Reflection\Types\Boolean;
use yii\base\ErrorException;

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

    /**
     * Gets list of mapping templates in onesolr templates directory
     *
     * @return Array
     */
    public function getMappingTemplates() {
        try {
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
        } catch (ErrorException $e) {
            $files = [];
        }

        return $files;
    }

    /**
     * Gets list of mapping templates in onesolr templates directory
     *
     * @param Int $sectionId
     * @return String
     */
    public function getMappingBySectionId(Int $sectionId)
    {
        $mappingPath = MappingPathRecord::find()->where(['sectionId' => $sectionId])->one();
        if (!$mappingPath) {
            return false;
        }
        return $mappingPath->mappingPath;
    }

    /**
     * Saves an array of mapping path records
     *
     * @param Array $mappingPathData
     * @return Boolean
     */
    public function saveMappingPaths(Array $mappingPathData)
    {
        foreach ($mappingPathData as $mappingPath) {
            $mappingPathRecord = new MappingPathRecord();
            $mappingPathRecord->sectionId = $mappingPath['sectionId'];
            $mappingPathRecord->mappingPath = $mappingPath['mapping'];
            if (!$this->saveMappingBySectionId($mappingPathRecord)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Saves a mapping path by sectionId
     *
     * @param MappingPathRecord $mappingPathRecord
     * @return Boolean
     */
    public function saveMappingBySectionId(MappingPathRecord $mappingPathRecord) {
        $existingRecord = MappingPathRecord::find()->where(['sectionId' => $mappingPathRecord->sectionId])->one();

        // No mapping path found for this section, create a new one.
        if (!$existingRecord)
        {
            // But only when the mapping path is not empty.
            if (!empty($mappingPathRecord->mappingPath))
            {
                if (!$mappingPathRecord->validate()) {
                    return false;
                }
                $mappingPathRecord->save();
            }
        }
        // Mapping path found for this section.
        else
        {
            // Delete it if value is empty.
            if (empty($mappingPathRecord->mappingPath))
            {
                $existingRecord->delete();
            }
            // Else update the record with a new mapping path.
            else
            {
                $existingRecord->mappingPath = $mappingPathRecord->mappingPath;
                if (!$existingRecord->validate()) {
                    return false;
                }
                $existingRecord->save();
            }
        }
        return true;
    }

    /**
     * Gets all mappings
     *
     * @return Array
     */
    public function getMappings()
    {
        return MappingPathRecord::find()->all();
    }
}
