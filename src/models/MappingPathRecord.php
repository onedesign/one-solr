<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolr\models;

use onedesign\onesolr\OneSolr;

use Craft;
use craft\db\ActiveRecord;

/**
 * MappingPath Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 */
class MappingPathRecord extends ActiveRecord
{

    // Public Methods
    // =========================================================================

    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{%onesolr_mappingpaths}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['sectionId', 'required'],
            ['mappingPath', 'required']
        ];
    }
}
