<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name')}} | @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <v-app>
            @if(Auth::check())
                <nav-component
                        :usuario="{{Auth::user()}}"
                ></nav-component>
                <aside-component
                        :usuario="{{Auth::user()}}"
                        :roles="{{Auth::user()->roles->pluck('id_rol')}}"
                        :storage="'{{\Illuminate\Support\Facades\Storage::url('img_user')}}'"
                ></aside-component>
            @endif
            <v-content>
                <v-container fluid>
                    <main class="py-4">
                        @yield('content')
                    </main>
                    <!--<alert-component></alert-component>-->
                    <v-footer
                            padless
                            :absolute=true
                            color="grey lighten-2">
                        <v-col
                                class="text-center"
                                cols="12"
                        >
                            <strong>{{now()->format('Y')}} Dasalflor - Facturación electrónia </strong>
                        </v-col>
                    </v-footer>
                </v-container>
            </v-content>

        </v-app>
    </div>
</body>
</html>
