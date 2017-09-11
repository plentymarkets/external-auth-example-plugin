<?php

namespace ExternalAuthExample\Controllers;

use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\ExternalAuth\Contracts\ExternalAccessRepositoryContract;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Templates\Twig;
use Plenty\Modules\Frontend\Services\AccountService;

class ContentController extends Controller
{
    public function showLoginPage(Twig $twig, AccountService $accountService, ContactRepositoryContract $contactRepo)
    {
        $user = null;
        if ($accountService->getIsAccountLoggedIn()) {
            $userId = $accountService->getAccountContactId();
            $user = $contactRepo->findContactById($userId);
        }

        return $twig->render(
            'ExternalAuthExample::content.login', [
            'user' => $user,
        ]);
    }

    public function showUserHomepage(
        Twig $twig,
        AccountService $accountService,
        ContactRepositoryContract $contactRepo,
        ExternalAccessRepositoryContract $eaRepo,
        Response $response
    ) {
        if (!$accountService->getIsAccountLoggedIn()) {
            return $response->redirectTo('/login');
        }

        $userId = $accountService->getAccountContactId();
        $user = $contactRepo->findContactById($userId);
        $googleConnection= $eaRepo->findForTypeAndContactId('Google', $userId);

        return $twig->render('ExternalAuthExample::content.home', [
            'user' => $user,
            'googleConnection' => $googleConnection ?? null,
        ]);
    }
}