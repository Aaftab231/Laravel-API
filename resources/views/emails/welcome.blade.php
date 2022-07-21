
@component('mail::message')
    # Hey {{$user->name}}

    Thank you for create an account. please verify your email using this link:

    @component('mail::button', ['url' => route("verify", $user->verification_token)])
        Verify Account
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
