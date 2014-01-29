<?php

namespace MajorApi\Tests\Functional;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\IppClient;
use MajorApi\Tests\DataFixtures\FixtureLoader;

use Doctrine\Common\DataFixtures\Loader;

use \PHPUnit_Framework_TestCase,
    \DateTime;

class TestCase extends PHPUnit_Framework_TestCase
{

    /** @var string */
    protected $fixtureDir = '';

    /** @var array */
    protected $account = false;

    /** @var array */
    protected $application = false;

    public function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../../../../app/fixtures/');

        $fixtureLoader = new FixtureLoader;
        $fixtureLoader->load();
    }

    public function getAccountFixture($force=false)
    {
        // Returns a MajorApi Account entity. If the record does not
        // exist, it is created.
        if (!$this->account || $force) {
            $testAccountEmail = Registry::getMajorApiConfig()['test_account_email'];

            $query = "SELECT a.* FROM web_account a WHERE a.email = ?";
            $this->account = Registry::getPostgres()
                ->fetchAssoc($query, [$testAccountEmail]);
        }

        return $this->account;
    }

    public function getApplicationFixture($force=false)
    {
        // Returns a MajorApi Application entity. If the record does not
        // exist, it is created.
        if (!$this->application || $force) {
            $testApplicationUsername = Registry::getMajorApiConfig()['test_application_username'];

            $query = "SELECT a.* FROM web_application a WHERE a.username = ?";
            $this->application = Registry::getPostgres()
                ->fetchAssoc($query, [$testApplicationUsername]);
        }

        return $this->application;
    }

    public function getIppApplicationFixture()
    {
        $application = $this->getApplicationFixture();
        $majorApiConfig = Registry::getMajorApiConfig();

        $ipp = [
            'quickbooks_type' => IppClient::TYPE_DESKTOP,
            'quickbooks_realm_id' => $majorApiConfig['test_ipp_realm_id'],
            'quickbooks_oauth_token' => $majorApiConfig['test_ipp_oauth_token'],
            'quickbooks_oauth_token_secret' => $majorApiConfig['test_ipp_oauth_token_secret']
        ];

        Registry::getPostgres()->update('web_application', $ipp, ['id' => $application['id']]);

        return $this->getApplicationFixture(true);
    }

}
