<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-xl shadow-2xl border border-gray-700">
        <h1 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-4 text-blue-400">Artisan Web Console</h1>

        <div class="grid grid-cols-1 gap-4">
            @php
                $cmds = [
                    'Clear All (Optimize Clear)' => 'clear-all',
                    'Re-Optimize (Cache All)' => 'optimize',
                    'Config Cache' => 'config-cache',
                    'Clear View Cache' => 'view-clear',
                    'Run Migrations' => 'migrate',
                    'Create Storage Link' => 'storage-link',
                ];
            @endphp

            @foreach ($cmds as $label => $slug)
                <a href="{{ url('/admin/terminal/run/' . $slug) }}"
                    class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-4 rounded transition text-center shadow-lg">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="mt-8 p-4 bg-yellow-900/30 border border-yellow-700 rounded text-yellow-200 text-sm">
            <strong>Local Environment Note:</strong> If "Create Storage Link" fails on Windows, remember to restart your
            local server (Artisan Serve) as an Administrator.
        </div>
    </div>
</body>

</html>
