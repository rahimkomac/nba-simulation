<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div id="app">
        <v-app id="inspire">
            <stat-component :playing='@json($isPlaying)' :url="'{{ route('nextFixture') }}'" :schedule="'{{ $schedule }}'" :match-data='@json($matchData)'></stat-component>
        </v-app>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>

