<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Outlet summary --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Signed in as {{ Auth::user()->name }}</p>

                    @if ($outlet)
                        <h3 class="mt-1 text-lg font-semibold">{{ $outlet->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Outlet #{{ $outlet->id }} · Merchant #{{ $outlet->merchant_id }}</p>

                        <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-md bg-gray-50 dark:bg-gray-700 p-4">
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300">Active menu items</dt>
                                <dd class="mt-1 text-2xl font-semibold">{{ $activeCount }}</dd>
                            </div>
                            <div class="rounded-md bg-gray-50 dark:bg-gray-700 p-4">
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300">With AI labels</dt>
                                <dd class="mt-1 text-2xl font-semibold">{{ $labelledCount }} / {{ $activeCount }}</dd>
                            </div>
                            <div class="rounded-md bg-gray-50 dark:bg-gray-700 p-4">
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300">Last menu sync</dt>
                                <dd class="mt-1 text-base font-semibold">{{ $lastSynced ? \Illuminate\Support\Carbon::parse($lastSynced)->diffForHumans() : 'Never' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                            Your account is not attached to an outlet yet, so the menu, customer and AI features have nothing to show.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Navigation cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('product.index') }}"
                   class="block bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:shadow-md hover:ring-2 hover:ring-indigo-500 transition">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Menu &amp; AI labels</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Sync the outlet's menu from the POS and generate AI labels for it.
                    </p>
                    <span class="mt-4 inline-block text-sm font-medium text-indigo-600 dark:text-indigo-400">Open products &rarr;</span>
                </a>

                <a href="{{ route('customer') }}"
                   class="block bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:shadow-md hover:ring-2 hover:ring-indigo-500 transition">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Customer display</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        The camera screen the customer faces. It scans, then shows the recommended dish.
                    </p>
                    <span class="mt-4 inline-block text-sm font-medium text-indigo-600 dark:text-indigo-400">Open customer screen &rarr;</span>
                </a>

                <a href="{{ route('cashier') }}"
                   class="block bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:shadow-md hover:ring-2 hover:ring-indigo-500 transition">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Cashier</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Trigger the customer's scan remotely and read the result (age, mood, weather, pick).
                    </p>
                    <span class="mt-4 inline-block text-sm font-medium text-indigo-600 dark:text-indigo-400">Open cashier screen &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
