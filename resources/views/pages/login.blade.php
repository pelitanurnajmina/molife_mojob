<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molife — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#F8F9FA]">
    <div class="w-full max-w-md p-6">
        <div class="bg-white rounded-3xl p-8 md:p-10 border border-gray-100">
            <div class="flex flex-col items-center mb-8">
                <div class="w-16 h-16 bg-black rounded-2xl flex items-center justify-center text-white mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-2">Molife</h1>
                <p class="text-sm text-gray-500">Track your spiritual, physical & personal life</p>
            </div>

            {{-- Error Message --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm font-medium">
                {{ $errors->first('username') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Username</label>
                    <input type="text" name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        class="w-full p-3 bg-gray-50 border {{ $errors->has('username') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autofocus required>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" name="password"
                        placeholder="Masukkan password"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        required>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer">
                    <label for="remember" class="text-sm text-gray-500 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all mt-2">
                    Masuk
                </button>
            </form>

            <p class="text-xs text-center text-gray-400 mt-6 flex items-center justify-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Data tersimpan di database lokal
            </p>
        </div>
    </div>
</body>
</html>
