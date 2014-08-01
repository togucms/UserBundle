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

namespace Togu\UserBundle\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
	private $router;
	private $session;

	/**
	 * Constructor
	 *
	 * @param     RouterInterface $router
	 * @param     Session $session
	 */
	public function __construct( RouterInterface $router, Session $session )
	{
		$this->router  = $router;
		$this->session = $session;
	}

	/**
	 * onAuthenticationSuccess
	 *
	 * @param     Request $request
	 * @param     TokenInterface $token
	 * @return     Response
	 */
	public function onAuthenticationSuccess( Request $request, TokenInterface $token )
	{
		// if AJAX login
		if ( ! $request->isXmlHttpRequest() ) {
			throw new NotFoundHttpException('Must use AJAX Login');
		}

		return new JsonResponse(array(
			"success" => true
		));
	}

	/**
	 * onAuthenticationFailure
	 *
	 * @param     Request $request
	 * @param     AuthenticationException $exception
	 * @return     Response
	 */
	public function onAuthenticationFailure( Request $request, AuthenticationException $exception )
	{
		// if AJAX login
		if ( ! $request->isXmlHttpRequest() ) {
			throw new NotFoundHttpException('Must use AJAX Login');
		}

		return new JsonResponse(array(
				"success" => false
		));
	}
}