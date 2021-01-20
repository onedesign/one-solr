<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolrtests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use onedesign\onesolr\OneSolr;

/**
 * MappingPathUnitTest
 *
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 */
class MappingPathUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */

    public function testGetSectionById()
    {
        $mapping = OneSolr::getInstance()->mappingPath->getMappingBySectionId(0);
        $this->assertSame(
            $mapping,
            false
        );
    }
}
