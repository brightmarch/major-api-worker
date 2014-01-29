<?php

namespace MajorApi\Quickbooks;

use \DOMDocument,
    \DOMXpath,
    \InvalidArgumentException,
    \OAuth,
    \ReflectionClass,
    \RuntimeException,
    \SimpleXMLElement;

abstract class IppClient
{

    /** @var OAuth */
    private $oauth;

    /** @var string */
    private $oauthConsumerKey = '';

    /** @var string */
    private $oauthConsumerSecret = '';

    /** @var integer */
    private $realmId = 0;

    /** @var array */
    private static $types = [
        self::TYPE_DESKTOP => 'MajorApi\Quickbooks\IppDesktopClient',
        self::TYPE_ONLINE => 'MajorApi\Quickbooks\IppOnlineClient'
    ];

    /** @var string */
    private $lastResponse = '';

    /** @var string */
    private $lastResponseErrorMessage = '';

    /** @var integer */
    private $lastResponseHttpCode = 200;

    /** @const string */
    const TYPE_DESKTOP = 'desktop';

    /** @const string */
    const TYPE_ONLINE = 'online';

    public function __construct($oauthConsumerKey, $oauthConsumerSecret)
    {
        $this->oauthConsumerKey = $oauthConsumerKey;
        $this->oauthConsumerSecret = $oauthConsumerSecret;
    }

    public function setOAuth(OAuth $oauth)
    {
        $this->oauth = $oauth;

        return $this;
    }

    public function connect($oauthToken, $oauthTokenSecret, $realmId)
    {
        $this->realmId = (int)$realmId;

        if (!$this->isConnected()) {
            $this->oauth = new OAuth(
                $this->oauthConsumerKey,
                $this->oauthConsumerSecret,
                OAUTH_SIG_METHOD_HMACSHA1,
                OAUTH_AUTH_TYPE_AUTHORIZATION
            );
        }

        $this->oauth->setToken($oauthToken, $oauthTokenSecret);

        return $this;
    }

    public function read($resource, $id=null)
    {
        $url = $this->getUrl($resource);

        if (!empty($id)) {
            $url = sprintf('%s/%s', $url, $id);
        }

        $this->oauth->fetch($url, [], OAUTH_HTTP_METHOD_GET);

        return $this->parseResponse();
    }

    public function create($resource, $requestXml)
    {
        $url = $this->getUrl($resource);
        $headers = [
            'Content-Type' => 'text/xml'
        ];

        $this->oauth->fetch($url, $requestXml, OAUTH_HTTP_METHOD_POST, $headers);

        return $this->parseResponse();
    }

    public function isConnected()
    {
        return (!is_null($this->oauth));
    }

    public function isSuccessfulRequest()
    {
        $successful = ($this->lastResponseHttpCode >= 200 && $this->lastResponseHttpCode <= 299);

        return $successful;
    }

    public function getUrl($resource)
    {
        if (!$this->isConnected()) {
            $message = sprintf("The IppClient is not connected to an OAuth provider and can not access the resource: %s.", $resource);
            throw new RuntimeException($message);
        }

        return sprintf($this->getUrlTemplate(), $resource, $this->realmId);
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    public function getLastResponseErrorMessage()
    {
        return $this->lastResponseErrorMessage;
    }

    public function getLastResponseHttpCode()
    {
        return $this->lastResponseHttpCode;
    }

    public function getRequestId()
    {
        // This is not for security reasons, just for uniqueness reasons.
        return (md5(uniqid() . (string)microtime(true)));
    }

    abstract public function getUrlTemplate();

    public static function getDesktopClient($oauthConsumerKey, $oauthConsumerSecret)
    {
        return self::getClient(self::TYPE_DESKTOP, $oauthConsumerKey, $oauthConsumerSecret);
    }

    public static function getOnlineClient($oauthConsumerKey, $oauthConsumerSecret)
    {
        return self::getClient(self::TYPE_ONLINE, $oauthConsumerKey, $oauthConsumerSecret);
    }

    public static function getClient($type, $oauthConsumerKey, $oauthConsumerSecret)
    {
        // No public interface is provided for this method so
        // it can only be called through getDesktopClient() or getOnlineClient().
        $type = strtolower($type);

        if (!array_key_exists($type, self::$types)) {
            $message = sprintf("A valid IPP client can not be constructed from the type: %s.", $type);
            throw new InvalidArgumentException($message);
        }

        $rc = new ReflectionClass(self::$types[$type]);
        $client = $rc->newInstance($oauthConsumerKey, $oauthConsumerSecret);
        unset($rc);

        return $client;
    }

    private function parseResponse()
    {
        try {
            $oauthInfo = $this->oauth->getLastResponseInfo();
            $responseXml = $this->oauth->getLastResponse();

            $this->lastResponse = $responseXml;
            $this->lastResponseHttpCode = (int)$oauthInfo['http_code'];
            $this->lastResponseErrorMessage = '';

            $dom = new DOMDocument;

            if (!$dom->loadXML($responseXml, LIBXML_NOERROR)) {
                throw new RuntimeException("The XML returned by the IPP API is invalid and can not be parsed.", 500);
            }

            // The IPP responses have their own namespace so we have to register a custom namespace
            // to properly query the document by Xpath.
            $xpath = new DOMXpath($dom);
            $namespaceUri = $dom->lookupNamespaceUri($dom->namespaceURI);
            if (!empty($namespaceUri)) {
                $xpath->registerNamespace('ipp', $namespaceUri);
            }

            // Ensure there is a <RestResponse> tag to validate the response.
            $nodeList = $xpath->query('//ipp:RestResponse');
            if (0 == $nodeList->length) {
                throw new RuntimeException("The XML returned by the IPP API does not contain a <RestResponse> tag and can not be properly parsed.", 500);
            }

            // Check to see if an actual error was returned.
            $nodeList = $xpath->query('//ipp:RestResponse/ipp:Error');

            if ($nodeList->length > 0) {
                $errorNode = $nodeList->item(0);
                $errorCodeNode = $xpath->query('ipp:ErrorCode', $errorNode)->item(0);
                $errorDescNode = $xpath->query('ipp:ErrorDesc', $errorNode)->item(0);

                if ($errorCodeNode && $errorDescNode) {
                    throw new RuntimeException($errorDescNode->textContent, (int)$errorCodeNode->textContent);
                }
            }
        } catch (RuntimeException $e) {
            $this->lastResponseHttpCode = (int)$e->getCode();
            $this->lastResponseErrorMessage = $e->getMessage();

            return false;
        }

        return true;
    }

}
