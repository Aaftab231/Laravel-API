Hey {{$user->name}}

You changed your email, So we need to verify this new address.

Please use the link below:-

{{route("verify", $user->verification_token)}}
