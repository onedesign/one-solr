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

use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Solarium\Exception\HttpException;

/**
 * Solr Service
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
class Solr extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Connect to solr server and create a client
     *
     * @return object SOLR Client
     */
    public function getSOLRClient()
    {
        $credentials = OneSolr::getInstance()->settings->credentials;
        // check that credentials are included in settings
        foreach (['host', 'port', 'path', 'core'] as $credential) {
            if (!array_key_exists($credential, $credentials)) {
                return false;
            }
        }
        $config = [
            'endpoint' => [
                'localhost' => $credentials
            ]
        ];
        $adapter = new Curl();
        $eventDispatcher = new EventDispatcher();
        return new Client($adapter, $eventDispatcher, $config);
    }

    /**
     * Is the SOLR server available?
     *
     * @return string
     */
    public function getPing()
    {
        $client = $this->getSOLRClient();
        if (!$client) {
            return 'No SOLR Client available, check credentials';
        }
        try {
            $ping = $client->createPing();
            $result = $client->ping($ping);
            return $result->getData()['status'];
        } catch (HttpException $e) {
            return 'Connection failed' . ' ' . $e->getMessage();
        }
    }
}
