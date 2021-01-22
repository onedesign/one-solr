<?php
/**
 * One Solr plugin for Craft CMS 3.x
 *
 * Craft Solr Integration
 *
 * @link      https://github.com/onedesign
 * @copyright Copyright (c) 2021 Michael Ramuta
 */

namespace onedesign\onesolr\controllers;

use onedesign\onesolr\OneSolr;

use Craft;
use craft\web\Controller;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Michael Ramuta
 * @package   OneSolr
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

    /**
     * Handles saving mapping path settings
     * e.g.: actions/one-solr/save-settings
     *
     * @return mixed
     */
    public function actionSaveSettings()
    {
        $mappingPathData = Craft::$app->request->getParam('mappingPaths');
        if (OneSolr::getInstance()->mappingPath->saveMappingPaths($mappingPathData)) {
            $this->setSuccessFlash('Mapping paths saved.');
        } else {
            Craft::$app->session->setFlash('notice', 'Couldn\'t save mapping paths');
            $this->setFailFlash('Couldn\'t save mapping paths.');
        }

        return $this->redirectToPostedUrl();
    }

    /**
     * Renders mapping preview
     * e.g.: actions/one-solr/render-mapping-preview
     *
     * @return mixed
     */
    public function actionRenderMappingPreview()
    {
        $params = Craft::$app->getRequest()->post();
        // todo: use correct template for base... I dont think if this can work
        return $this->renderTemplate( 'one-solr/mapping/' . $params['mapping'], $params);
    }
}
