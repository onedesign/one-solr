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
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.oneSolr.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.oneSolr.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function test()
    {
        return OneSolr::getInstance()->solr->getPing();
    }

    public function getMappingTemplates() {
        return OneSolr::getInstance()->mappingPath->getMappingTemplates();
    }
}
