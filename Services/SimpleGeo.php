<?php

/**
 * Services_SimpleGeo
 *
 * Implementation of the OAuth specification
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_SimpleGeo
 * @author    Joe Stump <joe@joestump.net>
 * @copyright 2010 Joe Stump <joe@joestump.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/Services_SimpleGeo
 * @link      http://github.com/simplegeo/Services_SimpleGeo
 */

require_once 'HTTP/OAuth/Consumer.php';
require_once 'Services/SimpleGeo/Exception.php';
require_once 'Services/SimpleGeo/Record.php';

/**
 * Services_SimpleGeo
 *
 * @category  Services
 * @package   Services_SimpleGeo
 * @author    Joe Stump <joe@joestump.net>
 * @copyright 2010 Joe Stump <joe@joestump.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/Services_SimpleGeo
 * @link      http://github.com/simplegeo/Services_SimpleGeo
 */
class Services_SimpleGeo 
{
    /**
     * Version of the API to use
     *
     * @var string $version The version of the API to use
     */
    private $version = '1.0';

    /**
     * Base URI of the API
     *
     * @var string $api The base URI for the SimpleGeo API
     */
    private $api = 'http://api.simplegeo.com';

    /**
     * OAuth client
     *
     * @var object $oauth Instance of OAuth client
     * @see HTTP_OAuth_Consumer
     */
    private $oauth = null;

    /**
     * Constructor
     *
     * @var string $token  Your OAuth token
     * @var string $secret Your OAuth secret
     *
     * @return void
     * @see HTTP_OAuth_Consumer
     */
    public function __construct($token, $secret, $version = '1.0')
    {
        $this->oauth   = new HTTP_OAuth_Consumer($token, $secret);
        $this->version = $version;
    }

    /**
     * Reverse geocode a lat/lon to an address
     *
     * @var float $lat Latitude
     * @var float $lon Longitude
     *
     * @return mixed
     */
    public function getAddress($lat, $lon)
    {
        return $this->_sendRequest('/nearby/address/' . $lat . ',' . 
            $lon . '.json');
    }

//    public function get

    /**
     * Send a request to the API
     *
     * @var string $endpoint Relative path to endpoint
     * @var array  $args     Additional arguments passed to HTTP_OAuth
     * @var string $method   HTTP method to use
     * 
     * @return mixed
     * @see HTTP_OAuth_Consumer::sendRequest()
     */
    private function _sendRequest($endpoint, $args = array(), $method = 'GET')
    {
        $url    = $this->api . '/' . $this->version . $endpoint;

        try {
            $result = $this->oauth->sendRequest($url, $args, $method);
        } catch (HTTP_OAuth_Exception $e) {
            throw new Services_SimpleGeo_Exception($e->getMessage(),
                $e->getCode());
        }

        $body   = @json_decode($result->getBody());
        if (substr($result->getStatus(), 0, 1) == '2') {
            return $body;
        }

        throw new Services_SimpleGeo_Exception($body['message'], 
            $result->getStatus());
    }
}

?>