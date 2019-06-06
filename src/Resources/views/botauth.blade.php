{{-- this is an example template, do not use it --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BotAuth</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
</head>
    <body class="h-100">

        <main role="main">
            <section class="jumbotron text-center bg-white">
                <div class="container">

                    <h1 class="jumbotron-heading">{{ __('Auth by bot example') }}</h1>

                    @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    @auth
                        <p class="lead text-muted">{{ __('Hi') }} {{ Auth::User()->name }}</p>

                        @if($logins)
                            {{ __('Auth by:') }}<br>
                            @foreach($logins as $login)
                                <div>{{ $login->provider }}, ID: {{ $login->external_id }}</div>
                            @endforeach
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            {{ csrf_field() }}
                            <input type="submit" class="btn btn-primary" value="Logout">
                        </form>
                    @else
                        <p>
                            {{ __('Write this message to the bot:') }} {{ $secret }}
                        </p>

                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="botsecret-input" value="{{ $secret }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="botsecret-btn" data-clipboard-target="#botsecret-input">Copy</button>
                                    </div>
                                    <script>
                                        new ClipboardJS('#botsecret-btn');
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="small">{{ __('Select bot') }}</div>

                        <a href="{{ config('botauth.vkontakte.link') }}" class="btn btn-link" target="_blank">VK</a>
                        <a href="{{ config('botauth.telegram.link') }}" class="btn btn-link" target="_blank">Telegram</a>
                        <a href="{{ config('botauth.facebook.link') }}" class="btn btn-link" target="_blank">Facebook</a>

                        <hr>

                        <form method="post" action="{{ route('botauth.check') }}" id="botauthform">
                            {{ csrf_field() }}
                            <input type="submit" class="btn btn-primary" value="{{ __('Check auth') }}">
                            <input type="hidden" name="secret" value="{{ $secret }}">
                        </form>

                        <script>
                            jQuery(document).ready(function() {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                setInterval(function() {
                                    var $form  = $('#botauthform'),
                                        _route = $form.attr('action');
                                        $.post(_route, $form.serialize())
                                        .done(function(res) {
                                            if(res && res.success === true)
                                            {
                                                location.reload();
                                            }
                                        });
                                }, 2500);
                            });
                        </script>
                    @endauth

                </div>
            </section>
        </main>

    </body>
</html>
