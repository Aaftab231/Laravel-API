
@component('mail::message')
    # Hey {{$user->name}}

    You changed your email, So we need to verify this new address.

    Please use the button below:-
    @component('mail::button', ['url' => route("verify", $user->verification_token)])
        Verify Account
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
