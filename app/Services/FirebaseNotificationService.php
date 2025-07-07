<?php
namespace App\Services;

use Google\Auth\OAuth2;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    protected string $projectId = 'armessenger-7e803'; // 🔁 à remplacer

    public function sendToDevice(string $token, string $title, string $body): bool
    {
        $serviceAccountPath = storage_path('app/firebase/armessenger-7e803-firebase-adminsdk-fbsvc-693c891fed.json');
        if (!file_exists($serviceAccountPath)) {
            \Log::error('❌ Le fichier de credentials Firebase est introuvable.');
        }

        $credentials = json_decode(file_get_contents($serviceAccountPath), true);
        \Log::info('✅ Credentials chargés');
        $oauth = new OAuth2([
            'audience' => 'https://oauth2.googleapis.com/token',

            'issuer' => $credentials['client_email'],
            'signingAlgorithm' => 'RS256',
            'signingKey' => $credentials['private_key'],
            'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging', // ✅ Ajout essentiel
        ]);



                try {
            $accessToken = $oauth->fetchAuthToken()['access_token'];
            \Log::info('✅ Token FCM généré');
        } catch (\Throwable $e) {
            \Log::error('❌ Erreur génération token FCM', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }


        $response = Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ],
            ]
        );

        return $response->successful();
    }
}
