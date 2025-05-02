<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface 
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse //  méthode est exécuté dès que Symfony détecte une tentative d'accès interdit

    {
       return new RedirectResponse('/') ; // on redirige l'utilisateur vers la page d'accueil quand l'accès est interdit
    }

}