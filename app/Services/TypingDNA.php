<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class TypingDNA
{
    private static $instance = null;
    private $apiKey = null;
    private $apiSecret = null;
    private $typingdna_url = null;
    private $secret = null;


    private function __construct()
    {
        $this->apiKey = env('TYPINGDNA_API_KEY');
        $this->apiSecret = env('TYPINGDNA_API_SECRET');
        $this->typingdna_url = env('TYPINGDNA_BASE_URL');
        $this->secret = env('TYPINGDNA_SECRET');
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function checkUser(User $user)
    {
        $userId = $this->generateUserID($user->id, $this->secret);
        $result = $this->check($userId);
        return  $result;
    }

    public function doAuto(User $user, $tp)
    {
        $userId = $this->generateUserID($user->id, $this->secret);


        $typingdna_url_de = urldecode($this->typingdna_url . '/auto/' . $userId);
        $response = Http::withToken(base64_encode("$this->apiKey:$this->apiSecret"), 'Basic')->post($typingdna_url_de, [
            'tp' => $tp
        ]);

        $result = $response->json();
        if ($result['status'] === 429) {
            sleep(1);
            $result = $this->doAuto($user, $tp);
        }
        return $result;
    }

    private function check($userid)
    {

        $typingdna_url = urldecode($this->typingdna_url . '/user/' . $userid);

        $response = Http::withToken(base64_encode("$this->apiKey:$this->apiSecret"), 'Basic')->get($typingdna_url);
        return $response->json();
    }

    private function generateUserID($userid, $privateKey)
    {
        return \md5($userid . $privateKey);
    }
}
