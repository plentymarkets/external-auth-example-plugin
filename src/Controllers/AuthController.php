<?php

namespace ExternalAuthExample\Controllers;

use Plenty\Modules\Authentication\Contracts\ContactAuthenticationRepositoryContract;
use Plenty\Modules\Frontend\Services\AccountService;
use Plenty\Plugin\Controller;
use Plenty\Plugin\ExternalAuth\Contracts\ExternalAccessRepositoryContract;
use Plenty\Plugin\ExternalAuth\Services\ExternalAuthService;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;

/**
 * Short description
 *
 * Longer, more detailed description
 *
 * @author Christoph Harms-Ensink
 */
class AuthController extends Controller
{
    const GOOGLE_TOKENINFO_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=';

    public function signInWithCredentials(
        Request $request,
        ContactAuthenticationRepositoryContract $contactAuth,
        Response $response
    ) {
        // TODO: validate input. Do NOT use this in production!
        $contactAuth->authenticateWithContactEmail($request->input('email'), $request->input('password'));

        return $response->redirectTo('/home');

    }

    public function signInWithToken(
        Request $request,
        ExternalAuthService $exAuthService,
        Response $response
    ) {
        $id_token = $request->input('idtoken');

        $externalUserData = $this->checkToken($id_token);

        return $exAuthService->logInWithExternalUserId($externalUserData['sub'], 'Google');
    }

    public function connectGoogleAccount(
        Request $request,
        ExternalAccessRepositoryContract $eaRepo,
        AccountService $accountService,
        Response $response
    ) {
        $id_token = $request->input('idtoken');

        $externalUserData = $this->checkToken($id_token);

        $ea = $eaRepo->create(
            [
                'contactId'         => $userId = $accountService->getAccountContactId(),
                'accessType'        => 'Google',
                'externalContactId' => $externalUserData['sub'],
            ]);

        return $ea;
    }

    public function logout(Response $response, ContactAuthenticationRepositoryContract $contactAuth)
    {
        $contactAuth->logout();

        return $response->redirectTo('/login');
    }

    protected function checkToken($token)
    {
        $curl = curl_init(self::GOOGLE_TOKENINFO_ENDPOINT . urlencode($token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res, true);
    }
}
