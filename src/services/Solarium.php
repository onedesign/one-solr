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

use craft\elements\Entry;
use onedesign\onesolr\OneSolr;

use Craft;
use craft\base\Component;

/**
 * Solarium Service
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
class Solarium extends Component
{
    private $client;
    private $update;

    public function __construct($config = [])
    {
        parent::__construct($config);

        // Get the SOLR client
        $this->client = OneSolr::getInstance()->solr->getSOLRClient();

        // Create a new update
        $this->update = $this->client->createUpdate();
    }
    // Public Methods
    // =========================================================================

    public function runIndexSectionSolr($params, $renderedContent)
    {
        $jsonContent = json_decode($renderedContent);
        if (!$jsonContent) {
            return true;
        }
        foreach(['limit', 'sectionId', 'mappingPath'] as $param) {
            if (!array_key_exists($param, $params) || !$params[$param]) {
                return false;
            }
        }
        // If it's a full update, delete everything belonging to the current mapping
        if (array_key_exists('fullUpdate', $params) && $params['fullUpdate'] && $params['offset'] == 0)
        {
            $this->clearDocumentsBySection($params['sectionId']);
        }

        // Create the documents
        foreach ($jsonContent as $data)
        {
            $this->createDocument($data);
        }

        // Add the commit to the update object
        $this->update->addCommit();

        // Go go go!
        $this->client->update($this->update);

        return true;
    }

    /**
     * Delete all documents in a section by it's sectionId
     * Won't execute immediately by default
     *
     * @param int sectionId
     * @param bool execute, default false
     * @return void
     */
    public function clearDocumentsBySection($sectionId, $execute = false)
    {
        $sectionIdField = OneSolr::getInstance()->settings->sectionIdField;

        $this->update->addDeleteQuery($sectionIdField . ':' . $sectionId);

        if($execute) {
            $this->update->addCommit();
            $this->client->update($this->update);
        }
    }

    /**
     * Loop through the mapping data and build the document
     *
     * @return void
     * @todo integrate recursion for multiple depth levels
     */
    private function createDocument($data)
    {
        $doc = $this->update->createDocument();

        foreach($data as $key=>$row)
        {
            $doc->$key = $row;
        }

        $this->update->addDocument($doc);
    }
}
