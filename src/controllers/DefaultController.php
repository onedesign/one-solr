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
use craft\elements\Entry;

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
        return $this->renderTemplate( 'onesolr/' . $params['mapping'], $params, 'site');
    }

    /**
     * Runs index for a section
     * e.g.: actions/one-solr/run-index
     *
     * @return mixed
     */
    public function actionRunIndex()
    {
        $params = Craft::$app->getRequest()->post();
        $view = $this->getView();
        $renderedContent = $view->renderPageTemplate( 'onesolr/' . $params['mappingPath'], $params, 'site');
        $params['status'] = OneSolr::getInstance()->solarium->runIndexSectionSolr($params, $renderedContent);
        return $this->asJson($params);
    }

    /**
     * Runs index for a section
     * e.g.: actions/one-solr/total-entries
     *
     * @return mixed
     */
    public function actionTotalEntries($sectionId)
    {
        $total = $entries = Entry::find()
            ->sectionId($sectionId)
            ->count();
        $mappingPath = OneSolr::getInstance()->mappingPath->getMappingBySectionId($sectionId);
        $data = [
            'total' => $total,
            'sectionId' => $sectionId,
            'mappingPath' => $mappingPath,
        ];
        return $this->renderTemplate( 'one-solr/total.json', $data);
    }
}
