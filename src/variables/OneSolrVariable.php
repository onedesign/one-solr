<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolr\variables;

use craft\models\Section;
use onedesign\onesolr\OneSolr;

use Craft;

/**
 * One Solr Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.oneSolr }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 */
class OneSolrVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Gets an array of mapping templates
     *
     * @return Array
     */
    public function getMappingTemplates() {
        return OneSolr::getInstance()->mappingPath->getMappingTemplates();
    }

    /**
     * Gets mapping for a section id
     *
     * @param Section $section
     * @return String
     */
    public function mappingsForSection(Section $section) {
        return OneSolr::getInstance()->mappingPath->getMappingBySectionId($section->id);
    }

    /**
     * Gets the ping status
     *
     * @return String
     */
    public function pingStatus() {
        return OneSolr::getInstance()->solr->getPing();
    }

    /**
     * Gets the step size
     *
     * @return Int
     */
    public function stepSize() {
        return OneSolr::getInstance()->settings->stepSize;;
    }

    /**
     * Gets all mappings
     *
     * @return Array
     */
    public function getMappings() {
        return OneSolr::getInstance()->mappingPath->getMappings();
    }
}
