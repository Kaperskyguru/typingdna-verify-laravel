<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div class="d-flex flex-row justify-content-between justify-content-center">
            <p>Two-Factor Authentication. Please click on the button below and type the text.</p>
            <div class="mt-5 justify-content-center">
                <x-button class="typingdna-verify" data-typingdna-client-id="{{session('typingDNA')['clientId']}}" data-typingdna-application-id="{{session('typingDNA')['applicationId']}}" data-typingdna-payload="{{session('typingDNA')['payload']}}" data-typingdna-callback-fn="callbackFn"> Verify with TypingDNA
                </x-button>
            </div>
        </div>


    </x-auth-card>

    <x-slot name="script_h">
        <script src="https://cdn.typingdna.com/verify/typingdna-verify.js"></script>
    </x-slot>

    <x-slot name="script">

        <script>
            function callbackFn(payload) {

                const token = '{{ Session::token() }}'

                var xhr = new XMLHttpRequest();
                xhr.open("POST", '/verifydna', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({
                    otp: payload['otp'],
                    '_token': token
                }));

            }
        </script>
    </x-slot>
</x-guest-layout>