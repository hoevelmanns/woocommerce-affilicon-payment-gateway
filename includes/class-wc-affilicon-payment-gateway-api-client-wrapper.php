<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        WC_Affilicon_Payment_Gateway_API_Client.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        24.10.17
 */

include_once 'affilicon-php-api-client/src/config/routes.php';
include_once 'affilicon-php-api-client/src/interfaces/ClientInterface.php';

//todo HttpService for woocommerce
include_once 'affilicon-php-api-client/src/interfaces/HttpServiceInterface.php';
include_once 'affilicon-php-api-client/src/interfaces/HttpServiceInterface.php';
include_once 'affilicon-php-api-client/src/interfaces/ModelInterface.php';
include_once 'affilicon-php-api-client/src/abstracts/AbstractHttpService.php';
include_once 'affilicon-php-api-client/src/interfaces/ProductInterface.php';
include_once 'affilicon-php-api-client/src/Client.php';

// todo write wrapper
//include_once 'affilicon-php-api-client/src/Request.php';
//include_once 'affilicon-php-api-client/src/Response.php';

include_once 'affilicon-php-api-client/src/services/HttpService.php';
include_once 'affilicon-php-api-client/src/models/Collection.php';
include_once 'affilicon-php-api-client/src/exceptions/ClientExceptions.php';
include_once 'affilicon-php-api-client/src/exceptions/CartCreationFailed.php';
include_once 'affilicon-php-api-client/src/exceptions/AuthenticationFailed.php';
include_once 'affilicon-php-api-client/src/exceptions/KeyHasUseException.php';
include_once 'affilicon-php-api-client/src/exceptions/KeyInvalidException.php';
include_once 'affilicon-php-api-client/src/abstracts/AbstractModel.php';
include_once 'affilicon-php-api-client/src/models/Cart.php';
include_once 'affilicon-php-api-client/src/models/Product.php';
include_once 'affilicon-php-api-client/src/models/LineItem.php';
