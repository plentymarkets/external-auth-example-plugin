<?php

namespace ExternalAuthExample\Controllers;

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

    public function signInWithToken(Request $request, ExternalAuthService $exAuthService)
    {
        $id_token = $request->input('idtoken');

        $externalUserData = $this->checkToken($id_token);

        $exAuthService->logInWithExternalUserId($externalUserData['sub'], 'Google');

        return $externalUserData;
    }

    protected function checkToken($token)
    {
        $curl = curl_init(self::GOOGLE_TOKENINFO_ENDPOINT . urlencode($token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}
