<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Model\Instagram;
/**
 * Instagram API class
 *
 * API Documentation: http://instagram.com/developer/
 * Class Documentation: https://github.com/cosenary/Instagram-PHP-API
 *
 * @author Christian Metz
 * @since 30.10.2011
 * @copyright Christian Metz - MetzWeb Networks 2011-2014
 * @version 2.2
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class Api
{
    /**
     * The API base URL.
     */
    const API_URL = 'https://api.instagram.com/v1/';

    /**
     * The API OAuth URL.
     */
    const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';

    /**
     * The OAuth token URL.
     */
    const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

    /**
     * The Instagram API Key.
     *
     * @var string
     */
    private $_apikey;

    /**
     * The Instagram OAuth API secret.
     *
     * @var string
     */
    private $_apisecret;

    /**
     * The callback URL.
     *
     * @var string
     */
    private $_callbackurl;

    /**
     * The user access token.
     *
     * @var string
     */
    private $_accesstoken;

    /**
     * Whether a signed header should be used.
     *
     * @var bool
     */
    private $_signedheader = false;

    /**
     * Available scopes.
     *
     * @var string[]
     */
    private $_scopes = array('basic', 'likes', 'comments', 'relationships', 'public_content', 'follower_list');

    /**
     * Available actions.
     *
     * @var string[]
     */
    private $_actions = array('follow', 'unfollow', 'approve', 'ignore');

    /**
     * Rate limit.
     *
     * @var int
     */
    private $_xRateLimitRemaining;

    /**
     * Default constructor.
     *
     * @param array|string $config Instagram configuration data
     *
     * @throws Exception
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $this->setApiKey($config['apiKey']);
            $this->setApiSecret($config['apiSecret']);
            $this->setApiCallback($config['apiCallback']);
        } elseif (is_string($config)) {
            $this->setAccessToken($config);
        } else {
            throw new Exception('Error: __construct() - Configuration data is missing.');
        }
    }

    /**
     * API Callback URL Setter.
     *
     * @param string $apiCallback
     *
     * @return void
     */
    public function setApiCallback($apiCallback)
    {
        $this->_callbackurl = $apiCallback;
    }

    /**
     * Generates the OAuth login URL.
     *
     * @param string[] $scopes Requesting additional permissions
     *
     * @return string Instagram OAuth login URL
     *
     * @throws Exception
     */
    public function getLoginUrl($scopes = array('basic'))
    {
        if (is_array($scopes) && count(array_intersect($scopes, $this->_scopes)) === count($scopes)) {
            return self::API_OAUTH_URL . '?client_id=' . $this->getApiKey() . '&redirect_uri=' . urlencode($this->getApiCallback()) . '&scope=' . implode('+',
                    $scopes) . '&response_type=code';
        }

        throw new Exception("Error: getLoginUrl() - The parameter isn't an array or invalid scope permissions used.");
    }

    /**
     * API Key Getter
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apikey;
    }

    /**
     * API-key Setter
     *
     * @param string $apiKey
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->_apikey = $apiKey;
    }

    /**
     * API Callback URL Getter.
     *
     * @return string
     */
    public function getApiCallback()
    {
        return $this->_callbackurl;
    }

    /**
     * Search for a user.
     *
     * @param string $name Instagram username
     * @param int $limit Limit of returned results
     *
     * @return mixed
     */
    public function searchUser($name, $limit = 0)
    {
        $params = array();

        $params['q'] = $name;
        if ($limit > 0) {
            $params['count'] = $limit;
        }

        return $this->_makeCall('users/search', $params);
    }

    /**
     * The call operator.
     *
     * @param string $function API resource path
     * @param array $params Additional request parameters
     * @param string $method Request type GET|POST
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function _makeCall($function, $params = null, $method = 'GET')
    {
        if (!isset($this->_accesstoken)) {
            throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
        }

        $authMethod = '?access_token=' . $this->getAccessToken();

        $paramString = null;

        if (isset($params) && is_array($params)) {
            $paramString = '&' . http_build_query($params);
        }

        $apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);

        // we want JSON
        $headerData = array('Accept: application/json');

        if ($this->_signedheader) {
            $apiCall .= (strstr($apiCall, '?') ? '&' : '?') . 'sig=' . $this->_signHeader($function, $authMethod, $params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiCall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, count($params));
                curl_setopt($ch, CURLOPT_POSTFIELDS, ltrim($paramString, '&'));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $jsonData = curl_exec($ch);
        // split header from JSON data
        // and assign each to a variable
        list($headerContent, $jsonData) = explode("\r\n\r\n", $jsonData, 2);

        // convert header content into an array
        $headers = $this->processHeaders($headerContent);

        // get the 'X-Ratelimit-Remaining' header value
        if (isset($headers['X-Ratelimit-Remaining'])) {
            $this->_xRateLimitRemaining = trim($headers['X-Ratelimit-Remaining']);
        }

        if (!$jsonData) {
            throw new Exception('Error: _makeCall() - cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($jsonData);
    }

    /**
     * Access Token Getter.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accesstoken;
    }

    /**
     * Access Token Setter.
     *
     * @param object|string $data
     *
     * @return void
     */
    public function setAccessToken($data)
    {
        $token = is_object($data) ? $data->access_token : $data;

        $this->_accesstoken = $token;
    }

    /**
     * Sign header by using endpoint, parameters and the API secret.
     *
     * @param string
     * @param string
     * @param array
     *
     * @return string The signature
     */
    private function _signHeader($endpoint, $authMethod, $params)
    {
        if (!is_array($params)) {
            $params = array();
        }
        if ($authMethod) {
            list($key, $value) = explode('=', substr($authMethod, 1), 2);
            $params[$key] = $value;
        }
        $baseString = '/' . $endpoint;
        ksort($params);
        foreach ($params as $key => $value) {
            $baseString .= '|' . $key . '=' . $value;
        }
        $signature = hash_hmac('sha256', $baseString, $this->_apisecret, false);

        return $signature;
    }

    /**
     * Read and process response header content.
     *
     * @param array
     *
     * @return array
     */
    private function processHeaders($headerContent)
    {
        $headers = array();

        foreach (explode("\r\n", $headerContent) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
                continue;
            }

            list($key, $value) = explode(':', $line);
            $headers[$key] = $value;
        }

        return $headers;
    }

    /**
     * Get user info.
     *
     * @param int $id Instagram user ID
     *
     * @return mixed
     */
    public function getUser($id = 0)
    {
        if ($id === 0) {
            $id = 'self';
        }

        return $this->_makeCall('users/' . $id);
    }

    /**
     * Get user recent media.
     *
     * @param int|string $id Instagram user ID
     * @param int $limit Limit of returned results
     * @param int $min_id Return media later than this min_id
     * @param int $max_id Return media earlier than this max_id
     *
     * @return mixed
     */
    public function getUserMedia($id = 'self', $limit = 0, $min_id = null, $max_id = null)
    {
        $params = array();

        if ($limit > 0) {
            $params['count'] = $limit;
        }
        if (isset($min_id)) {
            $params['min_id'] = $min_id;
        }
        if (isset($max_id)) {
            $params['max_id'] = $max_id;
        }

        return $this->_makeCall('users/' . $id . '/media/recent', $params);
    }

    /**
     * Get the liked photos of a user.
     *
     * @param int $limit Limit of returned results
     * @param int $max_like_id Return media liked before this id
     *
     * @return mixed
     */
    public function getUserLikes($limit = 0, $max_like_id = null)
    {
        $params = array();

        if ($limit > 0) {
            $params['count'] = $limit;
        }
        if (isset($max_id)) {
            $params['max_like_id'] = $max_like_id;
        }

        return $this->_makeCall('users/self/media/liked', $params);
    }

    /**
     * DEPRECATED
     * Get the list of users this user follows
     *
     * @param int|string $id Instagram user ID.
     * @param int $limit Limit of returned results
     *
     * @return void
     */
    public function getUserFollows($id = 'self', $limit = 0)
    {
        $this->getFollows();
    }

    /**
     * Get the list of users the authenticated user follows.
     *
     * @return mixed
     */
    public function getFollows()
    {
        return $this->_makeCall('users/self/follows');
    }

    /**
     * DEPRECATED
     * Get the list of users this user is followed by.
     *
     * @param int|string $id Instagram user ID
     * @param int $limit Limit of returned results
     *
     * @return void
     */
    public function getUserFollower($id = 'self', $limit = 0)
    {
        $this->getFollower();
    }

    /**
     * Get the list of users this user is followed by.
     *
     * @return mixed
     */
    public function getFollower()
    {
        return $this->_makeCall('users/self/followed-by');
    }

    /**
     * Get information about a relationship to another user.
     *
     * @param int $id Instagram user ID
     *
     * @return mixed
     */
    public function getUserRelationship($id)
    {
        return $this->_makeCall('users/' . $id . '/relationship');
    }

    /**
     * Get the value of X-RateLimit-Remaining header field.
     *
     * @return int X-RateLimit-Remaining API calls left within 1 hour
     */
    public function getRateLimit()
    {
        return $this->_xRateLimitRemaining;
    }

    /**
     * Modify the relationship between the current user and the target user.
     *
     * @param string $action Action command (follow/unfollow/approve/ignore)
     * @param int $user Target user ID
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function modifyRelationship($action, $user)
    {
        if (in_array($action, $this->_actions) && isset($user)) {
            return $this->_makeCall('users/' . $user . '/relationship', array('action' => $action), 'POST');
        }

        throw new Exception('Error: modifyRelationship() | This method requires an action command and the target user id.');
    }

    /**
     * Search media by its location.
     *
     * @param float $lat Latitude of the center search coordinate
     * @param float $lng Longitude of the center search coordinate
     * @param int $distance Distance in metres (default is 1km (distance=1000), max. is 5km)
     *
     * @return mixed
     */
    public function searchMedia($lat, $lng, $distance = 1000)
    {
        return $this->_makeCall('media/search', array(
            'lat' => $lat,
            'lng' => $lng,
            'distance' => $distance
        ));
    }

    /**
     * Get media by its id.
     *
     * @param int $id Instagram media ID
     *
     * @return mixed
     */
    public function getMedia($id)
    {
        return $this->_makeCall('media/' . $id);
    }

    /**
     * Search for tags by name.
     *
     * @param string $name Valid tag name
     *
     * @return mixed
     */
    public function searchTags($name)
    {
        return $this->_makeCall('tags/search', array('q' => $name));
    }

    /**
     * Get info about a tag
     *
     * @param string $name Valid tag name
     *
     * @return mixed
     */
    public function getTag($name)
    {
        return $this->_makeCall('tags/' . $name);
    }

    /**
     * Get a recently tagged media.
     *
     * @param string $name Valid tag name
     * @param int $limit Limit of returned results
     * @param int $min_tag_id Return media before this min_tag_id
     * @param int $max_tag_id Return media after this max_tag_id
     *
     * @return mixed
     */
    public function getTagMedia($name, $limit = 0, $min_tag_id = null, $max_tag_id = null)
    {
        $params = array();

        if ($limit > 0) {
            $params['count'] = $limit;
        }
        if ($min_tag_id !== null) {
            $params['min_tag_id'] = $min_tag_id;
        }
        if ($max_tag_id !== null) {
            $params['max_tag_id'] = $max_tag_id;
        }

        return $this->_makeCall('tags/' . $name . '/media/recent', $params);
    }

    /**
     * Get a list of users who have liked this media.
     *
     * @param int $id Instagram media ID
     *
     * @return mixed
     */
    public function getMediaLikes($id)
    {
        return $this->_makeCall('media/' . $id . '/likes');
    }

    /**
     * Get a list of comments for this media.
     *
     * @param int $id Instagram media ID
     *
     * @return mixed
     */
    public function getMediaComments($id)
    {
        return $this->_makeCall('media/' . $id . '/comments');
    }

    /**
     * Add a comment on a media.
     *
     * @param int $id Instagram media ID
     * @param string $text Comment content
     *
     * @return mixed
     */
    public function addMediaComment($id, $text)
    {
        return $this->_makeCall('media/' . $id . '/comments', array('text' => $text), 'POST');
    }

    /**
     * Remove user comment on a media.
     *
     * @param int $id Instagram media ID
     * @param string $commentID User comment ID
     *
     * @return mixed
     */
    public function deleteMediaComment($id, $commentID)
    {
        return $this->_makeCall('media/' . $id . '/comments/' . $commentID, null, 'DELETE');
    }

    /**
     * Set user like on a media.
     *
     * @param int $id Instagram media ID
     *
     * @return mixed
     */
    public function likeMedia($id)
    {
        return $this->_makeCall('media/' . $id . '/likes', null, 'POST');
    }

    /**
     * Remove user like on a media.
     *
     * @param int $id Instagram media ID
     *
     * @return mixed
     */
    public function deleteLikedMedia($id)
    {
        return $this->_makeCall('media/' . $id . '/likes', null, 'DELETE');
    }

    /**
     * Get information about a location.
     *
     * @param int $id Instagram location ID
     *
     * @return mixed
     */
    public function getLocation($id)
    {
        return $this->_makeCall('locations/' . $id);
    }

    /**
     * Get recent media from a given location.
     *
     * @param int $id Instagram location ID
     * @param int $min_id Return media before this min_id
     * @param int $max_id Return media after this max_id
     *
     * @return mixed
     */
    public function getLocationMedia($id, $min_id = null, $max_id = null)
    {
        $params = array();

        if (isset($min_id)) {
            $params['min_id'] = $min_id;
        }
        if (isset($max_id)) {
            $params['max_id'] = $max_id;
        }

        return $this->_makeCall('locations/' . $id . '/media/recent', $params);
    }

    /**
     * Get recent media from a given location.
     *
     * @param float $lat Latitude of the center search coordinate
     * @param float $lng Longitude of the center search coordinate
     * @param int $distance Distance in meter (max. distance: 5km = 5000)
     * @param int $facebook_places_id Returns a location mapped off of a
     *                                Facebook places id. If used, a Foursquare
     *                                id and lat, lng are not required.
     * @param int $foursquare_id Returns a location mapped off of a foursquare v2
     *                                api location id. If used, you are not
     *                                required to use lat and lng.
     *
     * @return mixed
     */
    public function searchLocation($lat, $lng, $distance = 1000, $facebook_places_id = null, $foursquare_id = null)
    {
        $params['lat'] = $lat;
        $params['lng'] = $lng;
        $params['distance'] = $distance;
        if (isset($facebook_places_id)) {
            $params['facebook_places_id'] = $facebook_places_id;
        }
        if (isset($foursquare_id)) {
            $params['foursquare_id'] = $foursquare_id;
        }

        return $this->_makeCall('locations/search', $params);
    }

    /**
     * Pagination feature.
     *
     * @param object $obj Instagram object returned by a method
     * @param int $limit Limit of returned results
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function pagination($obj, $limit = 0)
    {
        if (is_object($obj) && !is_null($obj->pagination)) {
            if (!isset($obj->pagination->next_url)) {
                return;
            }

            $apiCall = explode('?', $obj->pagination->next_url);

            if (count($apiCall) < 2) {
                return;
            }

            $function = str_replace(self::API_URL, '', $apiCall[0]);
            $count = ($limit) ? $limit : count($obj->data);

            if (isset($obj->pagination->next_max_tag_id)) {
                return $this->_makeCall($function, array('max_tag_id' => $obj->pagination->next_max_tag_id, 'count' => $count));
            }

            return $this->_makeCall($function, array('next_max_id' => $obj->pagination->next_max_id, 'count' => $count));
        }
        throw new Exception("Error: pagination() | This method doesn't support pagination.");
    }

    /**
     * Get the OAuth data of a user by the returned callback code.
     *
     * @param string $code OAuth2 code variable (after a successful login)
     * @param bool $token If it's true, only the access token will be returned
     *
     * @return mixed
     */
    public function getOAuthToken($code, $token = false)
    {
        $apiData = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->getApiKey(),
            'client_secret' => $this->getApiSecret(),
            'redirect_uri' => $this->getApiCallback(),
            'code' => $code
        );

        $result = $this->_makeOAuthCall($apiData);
        return !$token ? $result : $result->access_token;
    }

    /**
     * API Secret Getter.
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->_apisecret;
    }

    /**
     * API Secret Setter
     *
     * @param string $apiSecret
     *
     * @return void
     */
    public function setApiSecret($apiSecret)
    {
        $this->_apisecret = $apiSecret;
    }

    /**
     * The OAuth call operator.
     *
     * @param array $apiData The post API data
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function _makeOAuthCall($apiData)
    {
        $apiHost = self::API_OAUTH_TOKEN_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiHost);
        curl_setopt($ch, CURLOPT_POST, count($apiData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $jsonData = curl_exec($ch);

        if (!$jsonData) {
            throw new Exception('Error: _makeOAuthCall() - cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($jsonData);
    }

    /**
     * Enforce Signed Header.
     *
     * @param bool $signedHeader
     *
     * @return void
     */
    public function setSignedHeader($signedHeader)
    {
        $this->_signedheader = $signedHeader;
    }
}
