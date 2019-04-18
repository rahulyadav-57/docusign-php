<?php

/*
 * This file is part of the DocuSignPHP package.
 *
 * (c) SqueegyCode <https://github.com/squeegycode>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//require_once('../../autoload.php');

namespace SqueegyCode\DocuSign;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class DocuSign extends Client {

	protected $auth_header;
	protected $accounts;

	public function __construct ( $username, $password, $integrator_key, $account_id = null, $env = 'demo', $version = 'v2' ) {
		$this->authenticate ( $username, $password, $integrator_key, $env, $version );
		$base_uri = $this->getBaseUri ( $account_id );
		if ( !$base_uri ) {
			throw new \GuzzleHttp\Exception\TransferException ( 'Could not determine a base URI.' );
		}
		parent::__construct ( [
			'base_uri' => $base_uri,
			'headers' => [
				'X-DocuSign-Authentication' => $this->auth_header
			]
		] );
	}

	protected function authenticate ( $username, $password, $integrator_key, $env, $version ) {
		$auth_header = [
			'Username' => $username,
			'Password' => $password,
			'IntegratorKey' => $integrator_key
		];
		$this->auth_header = \GuzzleHttp\json_encode ( $auth_header );
		$client = new Client(['verify' => false]);
		$response = $client->request ( 'GET', 'https://' . $env . '.docusign.net/restapi/' . $version . '/login_information', [
			'headers' => [
				'X-DocuSign-Authentication' => $this->auth_header
			]
			] );
		$content = \GuzzleHttp\json_decode ( (string) $response->getBody () );
		$this->accounts = $content->loginAccounts;
	}

	protected function getBaseUri ( $account_id = null ) {
		if ( !empty ( $this->accounts ) && is_array ( $this->accounts ) ) {
			foreach ( $this->accounts as $account ) {
				if ( NULL === $account_id && "true" === $account->isDefault ) {
					return $this->addEndingSlash ( $account->baseUrl );
				} elseif ( $account_id === $account->accountId ) {
					return $this->addEndingSlash ( $account->baseUrl );
				}
			}
		}
		return false;
	}

	protected function addEndingSlash ( $base_uri ) {
		if ( substr ( $base_uri, -1 ) !== '/' ) {
			$base_uri .= '/';
		}
		return $base_uri;
	}

}
