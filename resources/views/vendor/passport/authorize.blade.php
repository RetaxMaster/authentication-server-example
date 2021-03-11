<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - Authorization</title>

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/authorization.min.css') }}" rel="stylesheet">

</head>
<body>
    <div class="card">
        <div class="card-header">
            Authorization Request
        </div>
        <div class="card-body">
            <!-- Introduction -->
            <p><strong>{{ $client->name }}</strong> is requesting permission to access your account.</p>

            <!-- Scope List -->
            @if (count($scopes) > 0)
                <div class="scopes">
                        <p><strong>This application will be able to:</strong></p>

                        <ul>
                            @foreach ($scopes as $scope)
                                <li>{{ $scope->description }}</li>
                            @endforeach
                        </ul>
                </div>
            @endif

            <div class="buttons">
                <!-- Authorize Button -->
                <form method="post" action="{{ route('passport.authorizations.approve') }}">
                    @csrf

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="btn btn-success btn-approve">Authorize</button>
                </form>

                <!-- Cancel Button -->
                <form method="post" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button class="btn btn-danger">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
