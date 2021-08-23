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

        <form method="POST" action="{{ route('verifydna') }}" name="form" onsubmit="return validate(this)">
            @csrf
            <x-input name="textid" type="hidden" value="{{ session('textid') }}" />
            <x-input name="typingPattern" type="hidden" value="{{ session('typingPattern') }}" />
            <x-input type="hidden" name="email" value="{{ session('email') }}" />
            <x-input type="hidden" name="password" value="{{ session('password') }}" />



            <p>Two-Factor Authentication. Please type the text below</p>
            @if(session()->has('error_count'))
            <div>
                <em style="color: red; font-size:small; padding:2em; text-align:center" name="error" id="error">Remaining {{3 - session('error_count')-1}} more times.</em>
            </div>
            @endif
            <div style="margin: 2em;">
                <p style="font-weight: bold; text-align:center">I am authenticated by the way I type</p>
            </div>

            <!-- Email Address -->
            <div>
                <x-label for="Text" :value="__('Text')" />
                <x-input id="verifytext" class="block mt-1 w-full disable-autocomplete" type="text" name="verifytext" required autofocus />
            </div>

            <div class="flex items-center mt-4" style="justify-content: space-between;">
                <x-button class="mr-0">
                    {{ __('Submit') }}
                </x-button>
                <div>
                    <em class="ml-3" style="font-size: 10px;">Having troubles?</em>
                    <x-link href="/send-email" class="ml-3" id="email">
                        {{ __('Send Email') }}
                    </x-link>
                </div>
            </div>
        </form>

    </x-auth-card>
    <x-slot name="script_h">
        <script src="https://cdn.typingdna.com/verify/typingdna-verify.js"></script>
        <script src="{{ asset('js/autocomplete-disabler.js') }}"></script>
        <script src="{{ asset('js/typing-visualizer.js') }}"> </script>

    </x-slot>

    <x-slot name="script">
        <script>
            const tdna = new TypingDNA();
            tdna.addTarget('verifytext');
            var disabler = new AutocompleteDisabler({
                showTypingVisualizer: true,
                showTDNALogo: true
            });
            disabler.disableCopyPaste();
            disabler.disableAutocomplete();
            document.getElementById('verifytext').focus();

            function validate(form) {
                const error = document.getElementById("error")
                const user = form.verifytext.value.toString().trim();
                if (user === 'I am authenticated by the way I type') {
                    form.textid.value = TypingDNA.getTextId(user);
                    form.typingPattern.value = tdna.getTypingPattern({
                        type: 1,
                        text: user
                    });

                    return true;
                }
                error.innerHTML = "Please type in the word displayed below"
                return false
            }
        </script>
    </x-slot>
</x-guest-layout>