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

namespace Togu\UserBundle\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class AjaxAuthenticationListener
{

	/**
	 * Handles security related exceptions.
	 *
	 * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
	 */
	public function onCoreException(GetResponseForExceptionEvent $event)
	{
	    $exception = $event->getException();
	    $request = $event->getRequest();

    	if ($request->isXmlHttpRequest()) {
    		$msg = "";
    		if($exception instanceof AccessDeniedException ) {
    			$msg = "User Not Authenticated";
    		} elseif($exception instanceof AuthenticationCredentialsNotFoundException) {
    			$msg = "User Not Allowed";
    		} elseif($exception instanceof AuthenticationException) {
    			// Change this
    			$msg = "User Not Authenticated";
    		}

    		if($msg) {
    			$responseData = array('status' => 403, 'reason' => $msg);
    			$response = new JsonResponse();
    			$response->setData($responseData);
    			$response->setStatusCode($responseData['status']);
    			$event->setResponse($response);
    		}
	    }
	}
}