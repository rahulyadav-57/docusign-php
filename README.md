DocuSignPHP API Client [v1]
================================

Version 1 is now available!

If you used the beta version you will notice the code has completely changed.  This is not the same system as the beta.  This DocuSign API Client now contains 99% less bull and 100% more Guzzle!

DocuSignPHP is an open source PHP client designed to help jump start your project with using DocuSign's eSign REST API services.
To use this library you will need an account with DocuSign.  Visit their [Developer Center](https://www.docusign.com/developer-center) for more information.

Feel free to fork and contribute your own extensions to this library.


Installation
-------------------------

DocuSignPHP can be installed using [Composer](https://getcomposer.org/):

```
$ composer require squeegycode/docusign-php
```

or in your project's `composer.json` file:

```
"require": {
	"squeegycode/docusign-php": "~1.0"
}
```


Getting Started
-------------------------

DocuSignPHP API Client now utilizes Guzzle to communicate with DocuSign's API.  If you're not familiar with Guzzle you can check out their documentation [here](http://docs.guzzlephp.org/en/latest/overview.html).  You can find documentation for DocuSign's API [here](https://docs.docusign.com/esign/guide/usage/quickstart.html).

DocuSignPHP wraps Guzzle's client giving you access to it's request functions.  In addition, the constructor automatically authenticates and establishes a base URI for your requests without you having to do anything.  It also ensures the `X-DocuSign-Authentication` header is sent with every request.

```PHP
<?php

use SqueegyCode\DocuSign\DocuSign;

$username = 'johnnyboy@test.net';
$password = 'guessThis';
$integrator_key = 'abc-123-xyz-543';
$account_id = null;
$env = 'demo';
$version = 'v2';
 
// Construct the client and authenticate with DocuSign.
$client = new DocuSign( $username, $password, $integrator_key, $account_id, $env, $version );

// Create an Envelope from a Template
try {
	$response = $client->request('POST', 'envelopes' , [
		'json' => [
			'templateId' => 'abc-123',
			'emailBlurb' => 'Email Blurb',
			'emailSubject' => 'Subject Line',
			'status' => 'created',
			'customFields' => [
				'textCustomFields' => [
					[
						'name' => 'document_id',
						'show' => true,
						'required' => true,
						'value' => '12345'
					]
				]
			],
			'templateRoles' => [
				[
					'email' => 'johnnyboy@test.net',
					'name' => 'Johnny Boy',
					'roleName' => 'applicant'
				]
			]
		]
	]);
	
	echo \GuzzleHttp\Psr7\str($response);
} catch ( \GuzzleHttp\Exception\RequestException $e ) {
	echo \GuzzleHttp\Psr7\str($e->getRequest ());
	echo PHP_EOL;
	echo \GuzzleHttp\Psr7\str($e->getResponse ());
}

```

Easy as that!

When constructing a DocuSign client, you may specify an `$account_id`.  If given, the client will attempt to use the account returned during authentication that matches the ID you provided.  If that ID is not found, a `\GuzzleHttp\Exception\TransferException` error is thrown.  If no ID is provided, the client will use the account marked as the default by the DocuSign API.

All errors thrown are Guzzle Exceptions and should be handled by you, the developer.  You can learn more about Guzzle Exceptions [here](http://docs.guzzlephp.org/en/latest/quickstart.html#exceptions).