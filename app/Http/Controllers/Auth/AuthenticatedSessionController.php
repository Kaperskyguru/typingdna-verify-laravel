<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\TypingDNA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TypingDNAVerifyClient;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $request->authenticate();
        $request->session()->regenerate();
        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $user = User::where('email', auth()->user()->email);
        $user->is_real_user = false;
        $user->save();

        return redirect('/login');
    }

    public function verifyTypingDNA(Request $request)
    {

        $typingDNAClientID = env('TYPINGDNA_API_KEY');
        $typingDNAApplicationID = env('TYPINGDNA_APPLICATION_ID');
        $typingDNASECRET = env('TYPINGDNA_API_SECRET');

        $typingDNAVerifyClient = new TypingDNAVerifyClient($typingDNAClientID, $typingDNAApplicationID, $typingDNASECRET);

        $response = $typingDNAVerifyClient->validateOTP([
            'email' => '',
            'phoneNumber' => auth()->user()->phonenumber,
        ], $request->get('otp'));

        if ($response['success']) {
            $user = User::where('email', auth()->user()->email);
            $user->is_real_user = true;
            $user->save();
            return redirect(RouteServiceProvider::HOME);
        }
        $this->destroy($request);
    }
}
