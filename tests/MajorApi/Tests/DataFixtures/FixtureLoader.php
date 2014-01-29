<?php

namespace MajorApi\Tests\DataFixtures;

use MajorApi\Library\Registry;

use \DateTime;

class FixtureLoader
{

    public function load()
    {
        // Load an account and application fixture.
        Registry::getPostgres()->transactional(function ($postgres) {
            $majorApiConfig = Registry::getMajorApiConfig();

            // Purges the database.
            $postgres->createQueryBuilder()
                ->delete('web_account')
                ->execute();

            // Create a new Account entity.
            $account = [
                'created' => Registry::getTimeString(),
                'updated' => Registry::getTimeString(),
                'status' => Registry::STATUS_ENABLED,
                'email' => $majorApiConfig['test_account_email'],
                'password_hash' => $majorApiConfig['test_account_password'],
                'first_name' => 'Vic',
                'last_name' => 'Cherubini',
                'role' => 'ROLE_ACCOUNT'
            ];

            $postgres->insert('web_account', $account);
            $accountId = $postgres->lastInsertId('web_account_id_seq');

            // Create a new Application entity.
            $application = [
                'created' => Registry::getTimeString(),
                'updated' => Registry::getTimeString(),
                'status' => Registry::STATUS_ENABLED,
                'account_id' => $accountId,
                'name' => $majorApiConfig['test_application_username'],
                'username' => $majorApiConfig['test_application_username'],
                'api_key' => uniqid(),
                'quickbooks_token' => uniqid()
            ];

            $postgres->insert('web_application', $application);
            $applicationId = $postgres->lastInsertId('web_application_id_seq');
        });

        return true;
    }

}
