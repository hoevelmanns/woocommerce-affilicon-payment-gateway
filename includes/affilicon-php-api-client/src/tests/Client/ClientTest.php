<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file   ClientTest.php
 * @author Marcelle Hövelmanns
 * @site   http://www.artsolution.de
 * @date   29.10.17
 */

namespace AffiliconApiClient\Tests;

use AffiliconApiClient\Client;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testClient()
    {
        $client = new Client();
        $client->init();

        $this->assertClassHasStaticAttribute('instance', Client::class);
        $this->assertClassHasAttribute('environment', Client::class);

    }
}