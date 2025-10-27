<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-xl mt-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">👤 Profile Settings</h2>

        {{-- Status Message --}}
        @if (session('status'))
            <div class="mb-4 p-3 text-green-700 bg-green-100 border border-green-300 rounded-lg text-center">
                {{ session('status') }}
            </div>
        @endif

        {{-- Profile Update Form --}}
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input 
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
            </div>

            <button 
                type="submit"
                class="w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                💾 Save Changes
            </button>
        </form>

        <hr class="my-8 border-gray-300">

        {{-- Password Change Form --}}
        <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">🔒 Change Password</h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input 
                    type="password"
                    name="current_password"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                    required
                >
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input 
                    type="password"
                    name="password"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                    required
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input 
                    type="password"
                    name="password_confirmation"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                    required
                >
            </div>

            <button 
                type="submit"
                class="w-full py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                🔁 Update Password
            </button>
        </form>

        <hr class="my-8 border-gray-300">

        {{-- Delete Account Section --}}
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-2 text-center">⚠️ Delete Account</h3>
            <p class="text-gray-600 text-center mb-4">
                Once your account is deleted, all data will be permanently removed.
            </p>
            <div class="text-center">
                <button 
                    type="button"
                    onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors duration-200">
                    🗑️ Delete My Account
                </button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 shadow-xl w-96">
            <h4 class="text-lg font-semibold text-gray-800 mb-3 text-center">Are you sure?</h4>
            <p class="text-sm text-gray-600 mb-4 text-center">
                Please enter your password to confirm account deletion.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                    required
                >
                @error('password', 'userDeletion')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <div class="flex justify-between">
                    <button
                        type="button"
                        onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
