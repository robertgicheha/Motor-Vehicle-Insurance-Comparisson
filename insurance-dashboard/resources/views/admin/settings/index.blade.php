<x-app-layout>
    <div class="space-y-6 fade-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage system settings</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @foreach($settings as $group => $groupSettings)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white capitalize">{{ $group }}</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        @foreach($groupSettings as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 capitalize">
                                    {{ str_replace('_', ' ', $setting->key) }}
                                </label>
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Save Settings
            </button>
        </form>
    </div>
</x-app-layout>
