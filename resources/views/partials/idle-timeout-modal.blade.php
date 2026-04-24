<!-- Idle Timeout Warning Modal -->
<div id="idleTimeoutModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-slate-950 rounded-lg shadow-lg border border-slate-700 w-96 p-6">
        <h2 class="text-xl font-bold text-white mb-4">Session Expiring</h2>
        <p class="text-slate-300 mb-6">
            Your session is about to expire due to inactivity.
            Logging out in <span id="idleTimeoutCountdown" class="font-semibold text-white">0:00</span>.
            Click "Continue Session" to stay logged in.
        </p>
        <div class="flex gap-3 justify-end">
            <button onclick="document.getElementById('logoutForm').submit();" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                Logout
            </button>
            <button id="continueSession" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Continue Session
            </button>
        </div>
    </div>
</div>

<!-- Hidden logout form for timer expiration -->
<form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
    @csrf
</form>
