<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Services\TypingDNAVerifyClient;

class TwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $user = auth()->user();

        if (auth()->check()) {

            if (!$user->is_real_user) {

                $typingDNAClientID = env('TYPINGDNA_API_KEY');
                $typingDNAApplicationID = env('TYPINGDNA_APPLICATION_ID');
                $typingDNASECRET = env('TYPINGDNA_API_SECRET');

                $typingDNAVerifyClient = new TypingDNAVerifyClient($typingDNAClientID, $typingDNAApplicationID, $typingDNASECRET);

                $typingDNADataAttributes = $typingDNAVerifyClient->getDataAttributes([
                    'email' => '',
                    'phoneNumber' => $user->phonenumber,
                    'language' => 'EN',
                    'mode' => 'standard'
                ]);

                return redirect('/verify')->with("typingDNA", $typingDNADataAttributes);
            }

            return  redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }

    private function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
