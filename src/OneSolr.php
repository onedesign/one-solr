<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolr;

use onedesign\onesolr\services\Solarium as SolariumService;
use onedesign\onesolr\services\Solr as SolrService;
use onedesign\onesolr\variables\OneSolrVariable;
use onedesign\onesolr\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;
use craft\events\ElementEvent;
use craft\services\Elements;
use craft\elements\Entry;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 *
 * @property  SolariumService $solarium
 * @property  SolrService $solr
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class OneSolr extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * OneSolr::$plugin
     *
     * @var OneSolr
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * OneSolr::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'one-solr/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['one-solr/total/<sectionId:\d+>'] = 'one-solr/default/total-entries';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('oneSolr', OneSolrVariable::class);
            }
        );

        // After Entry save, if a mapping path exists with that sectionId:
        // Add to index if enabled, otherwise remove from index
        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            function(ElementEvent $event) {
                if ($event->element instanceof Entry) {
                    $entry = $event->element;
                    $section = $entry->section;
                    $mappingPath = OneSolr::getInstance()->mappingPath->getMappingBySectionId($section->id);
                    if (!$mappingPath) {
                        return $event;
                    }

                    if ($entry->enabled) {
                        OneSolr::getInstance()->solarium->indexEntry($entry, $mappingPath);
                    } else {
                        OneSolr::getInstance()->solarium->clearEntryFromSolr($entry->id);
                    }
                }
            });

        // Before entry delete, remove from index if it is in a section that has a
        // mapping path
        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_DELETE_ELEMENT,
            function(ElementEvent $event) {
                if ($event->element instanceof Entry) {
                    $entry = $event->element;
                    $section = $entry->section;
                    $mappingPath = OneSolr::getInstance()->mappingPath->getMappingBySectionId($section->id);
                    if (!$mappingPath) {
                        return $event;
                    }

                    OneSolr::getInstance()->solarium->clearEntryFromSolr($entry->id);
                }
            });

        $this->setComponents([
            'mappingPath' => \onedesign\onesolr\services\MappingPath::class,
        ]);

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'one-solr',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): array
    {
        $ret = parent::getCpNavItem();

        $ret['label'] = 'One Solr';

        $ret['subnav']['index'] = [
            'label' => 'Index',
            'url' => 'one-solr'
        ];

        $ret['subnav']['settings'] = [
            'label' => 'Settings',
            'url' => 'one-solr/settings'
        ];

        $ret['subnav']['status'] = [
            'label' => 'Status',
            'url' => 'one-solr/status'
        ];

        return $ret;
    }
}
