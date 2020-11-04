<?php
/**
 * Copyright 2020 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\TestUtils;

use Google\Cloud\TestUtils\GcloudWrapper\CloudFunction;
use GuzzleHttp\Client;

/**
 * Trait CloudFunctionLocalTestTrait.
 *
 * Uses the function framework to run the function as a local service for system tests.
 */
trait CloudFunctionLocalTestTrait
{
    use TestTrait;

    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var \Google\Cloud\TestUtils\GcloudWrapper\CloudFunction */
    private static $fn;

    /** @var \Symfony\Component\Process\Process; */
    private static $localhost;

    /**
     * Prepare and start the function in a local development server.
     *
     * @beforeClass
     */
    public static function startFunction()
    {
        $props = self::initFunctionProperties([
            'projectId' => self::requireEnv('GOOGLE_PROJECT_ID'),
        ]);
        self::$fn = CloudFunction::fromArray($props);
        self::$localhost = self::doRun();
    }

    /**
     * Start the development server based on the prepared function.
     *
     * Allows configuring server properties, for example:
     *
     *     return self::$fn->run(['FOO' => 'bar'], '9090', '/usr/local/bin/php');
     */
    private static function doRun()
    {
        return self::$fn->run();
    }

    /**
     * Configure CloudFunction properties.
     *
     * Example HTTP Function:
     *
     *     $props['entryPoint'] = 'helloHttp';
     *     return $props;
     *
     * Example CloudEvent Function:
     *
     *     $props['entryPoint'] = 'helloEvent';
     *     $props['functionSignatureType'] = 'cloudevent';
     *     return $props;
     */
    private static function initFunctionProperties(array $props = [])
    {
    }

    /**
     * Set up the client.
     *
     * @before
     */
    public function setUpClient()
    {
        $baseUrl = self::$fn->getLocalBaseUrl();
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'http_errors' => false
        ]);
    }

    /**
     * Stop the function.
     *
     * @afterClass
     */
    public static function stopFunction()
    {
        self::$fn->stop();
    }
}
