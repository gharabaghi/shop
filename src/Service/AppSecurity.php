<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

trait AppSecurity
{
    /**
     * @return bool
     * @throws InvalidCsrfTokenException if provided token was invalid.
     */
    private function checkCsrfToken(string $id, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid($id, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        return true;
    }
}
