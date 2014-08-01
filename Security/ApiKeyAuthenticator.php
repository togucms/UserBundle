<?php

/*
 * Copyright (c) 2012-2014 Alessandro Siragusa <alessandro@togu.io>
 *
 * This file is part of the Togu CMS.
 *
 * Togu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Togu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Togu.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Togu\UserBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\HttpFoundation\Response;


class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
	protected $userProvider;

	public function __construct(ApiKeyUserProvider $userProvider)
	{
		$this->userProvider = $userProvider;
	}

	public function createToken(Request $request, $providerKey)
	{
		if (!$request->query->has('apikey')) {
			throw new BadCredentialsException('No API key found');
		}

		return new PreAuthenticatedToken(
				'anon.',
				$request->query->get('apikey'),
				$providerKey
		);
	}

	public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
	{
		$apiKey = $token->getCredentials();
		$username = $this->userProvider->getUsernameForApiKey($apiKey);

		if (!$username) {
			throw new AuthenticationException(
					sprintf('API Key "%s" does not exist.', $apiKey)
			);
		}

		$user = $this->userProvider->loadUserByUsername($username);

		return new PreAuthenticatedToken(
				$user,
				$apiKey,
				$providerKey,
				$user->getRoles()
		);
	}

	public function supportsToken(TokenInterface $token, $providerKey)
	{
		return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		return new Response("Authentication Failed.", 403);
	}
}