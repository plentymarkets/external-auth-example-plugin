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
    /**
     * This is an endpoint provided by Google to check token authenticity. Due to the additional
     * request being made, it is not recommended to use this method in a production environment.
     * Refer to Google's docs for other methods to check token authenticity.
     */
    const GOOGLE_TOKENINFO_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=';

    /**
     * Sign in a user using email and password.
     *
     * @param Request                                 $request
     * @param ContactAuthenticationRepositoryContract $contactAuth
     * @param Response                                $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signInWithCredentials(
        Request $request,
        ContactAuthenticationRepositoryContract $contactAuth,
        Response $response
    ) {
        // TODO: validate input. Do NOT use this in production!
        $contactAuth->authenticateWithContactEmail($request->input('email'), $request->input('password'));

        return $response->redirectTo('/home');

    }

    /**
     * Sign the user in using a Google token
     *
     * @param Request             $request
     * @param ExternalAuthService $exAuthService
     *
     * @return mixed
     */
    public function signInWithGoogleToken(
        Request $request,
        ExternalAuthService $exAuthService
    ) {
        $id_token = $request->input('idtoken');

        $externalUserData = $this->checkToken($id_token);

        return $exAuthService->logInWithExternalUserId($externalUserData['sub'], 'Google');
    }

    /**
     * Connects the account of the currently logged-in Contact to a Google account. Creates a new ExternalAccess record in the database.
     *
     * @param Request                          $request
     * @param ExternalAccessRepositoryContract $eaRepo
     * @param AccountService                   $accountService
     *
     * @return \Plenty\Plugin\ExternalAuth\Models\ExternalAccess
     */
    public function connectGoogleAccount(
        Request $request,
        ExternalAccessRepositoryContract $eaRepo,
        AccountService $accountService
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

    /**
     * Log out the currently logged-in contact
     *
     * @param Response                                $response
     * @param ContactAuthenticationRepositoryContract $contactAuth
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Response $response, ContactAuthenticationRepositoryContract $contactAuth)
    {
        $contactAuth->logout();

        return $response->redirectTo('/login');
    }

    /**
     * Use the provided Google-Endpoint to check (and decode) a Google ID-token.
     *
     * @param $token
     *
     * @return mixed
     */
    protected function checkToken($token)
    {
        $curl = curl_init(self::GOOGLE_TOKENINFO_ENDPOINT . urlencode($token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res, true);
    }
}
