<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Content Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Product List</h3>
                            <p class="text-sm text-gray-500">
                                Menu items from the POS for your outlet, used for AI personalization.
                            </p>
                        </div>

                        @if ($outlet)
                            <div class="text-right text-sm">
                                <div class="font-medium text-gray-800">{{ $outlet->name }}</div>
                                <div class="text-gray-500">Outlet #{{ $outlet->id }} &middot; Merchant #{{ $outlet->merchant_id }}</div>
                            </div>
                        @endif
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @unless ($outlet)
                        <div class="mb-6 rounded border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            Your account is not attached to an outlet yet, so there is no menu to show.
                        </div>
                    @endunless

                    @if ($outlet)
                    <!-- Step 1: pull the outlet's menu from the POS into the unified table -->
                    <form method="POST" action="{{ route('product.sync') }}"
                          x-data="{ busy: false }" @submit="busy = true"
                          class="mb-3 flex flex-wrap items-center gap-3 rounded border border-gray-200 bg-gray-50 px-4 py-3">
                        @csrf
                        <div class="text-sm text-gray-800">
                            <span class="font-semibold">Menu sync</span>
                            <span class="text-gray-600">&mdash; pulls new and changed items for this outlet from the POS.</span>
                            <span class="ml-1 text-xs text-gray-500">
                                Last synced: {{ $lastSynced ? \Illuminate\Support\Carbon::parse($lastSynced)->diffForHumans() : 'never' }}
                            </span>
                        </div>
                        <x-secondary-button type="submit" class="ml-auto" x-bind:disabled="busy">
                            <span x-show="!busy">Sync menu</span>
                            <span x-show="busy" x-cloak>Syncing&hellip;</span>
                        </x-secondary-button>
                    </form>

                    <!-- Step 2: AI labelling for the outlet the user is logged into -->
                    <form method="POST" action="{{ route('product.labels') }}"
                          x-data="{ busy: false }" @submit="busy = true"
                          class="mb-6 flex flex-wrap items-center gap-3 rounded border border-indigo-100 bg-indigo-50 px-4 py-3">
                        @csrf
                        <div class="text-sm text-indigo-900">
                            <span class="font-semibold">AI menu labelling</span>
                            <span class="text-indigo-700">&mdash; tags each active product (course, cuisine, flavor, weather fit) so recommendations match the customer better.</span>
                        </div>
                        <label class="ml-auto flex items-center gap-1 text-sm text-gray-700">
                            <input type="checkbox" name="force" value="1" class="rounded border-gray-300">
                            Re-label all
                        </label>
                        <x-primary-button x-bind:disabled="busy">
                            <span x-show="!busy">Label menu</span>
                            <span x-show="busy" x-cloak>Labelling&hellip; this can take a minute</span>
                        </x-primary-button>
                    </form>
                    @endif

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-fixed min-w-[1100px] w-full border border-gray-200 whitespace-nowrap">
                            <colgroup>
                                <col style="width: 30%">
                                <col style="width: 0%">
                                <col style="width: 12%">
                                <col style="width: 18%">
                                <col style="width: 26%">
                                <col style="width: 7%">
                                <col style="width: 7%">
                            </colgroup>
                            
                            <thead class="bg-gray-100">

                                <!-- Column Header -->
                                <tr>
                                    <th class="px-4 py-2 border text-start" colspan="2">Name</th>
                                    <th class="px-4 py-2 border text-start">Price</th>
                                    <th class="px-4 py-2 border text-start">Description</th>
                                    <th class="px-4 py-2 border text-start">AI Labels</th>
                                    <th class="px-4 py-2 border text-center">Active</th>
                                    <th class="px-4 py-2 border"></th>
                                </tr>
                            
                                <!-- Filter Row -->
                                <form method="GET" action="{{ route('product.index') }}">
                                    <tr class="bg-gray-50">
                            
                                        <!-- Name -->
                                        <th colspan="2" class="px-3 py-2 border">
                                            <input type="text"
                                                   name="name"
                                                   value="{{ request('name') }}"
                                                   placeholder="Search name"
                                                   class="w-full text-sm rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        </th>
                            
                                        <!-- Price -->
                                        <th class="px-3 py-2 border">
                                            <input type="text"
                                                   name="price"
                                                   value="{{ request('price') }}"
                                                   placeholder="Search price"
                                                   class="w-full text-sm rounded border-gray-300">
                                        </th>
                            
                                        <!-- Description -->
                                        <th class="px-3 py-2 border">
                                            <input type="text"
                                                   name="description"
                                                   value="{{ request('description') }}"
                                                   placeholder="Search description"
                                                   class="w-full text-sm rounded border-gray-300">
                                        </th>
                            
                                        <!-- AI Labels (not searchable) -->
                                        <th class="px-3 py-2 border"></th>

                                        <!-- Active -->
                                        <th class="px-3 py-2 border">
                                            <select name="active"
                                                    class="w-full text-sm rounded border-gray-300">
                                                <option value="">All</option>
                                                <option value="active" {{ request('active') === 'active' ? 'selected' : '' }}>🟢 Active</option>
                                                <option value="inactive" {{ request('active') === 'inactive' ? 'selected' : '' }}>🔴 Inactive</option>
                                            </select>
                                        </th>
                            
                                        <!-- Action -->
                                        <th class="px-3 py-2 border text-sm text-center whitespace-nowrap">
                                            <button type="submit"
                                                    class="text-indigo-600 hover:underline">
                                                Search
                                            </button>
                                            |
                                            <a href="{{ route('product.index') }}"
                                               class="ml-2 text-red-600 hover:underline">
                                                Reset
                                            </a>
                                        </th>
                            
                                    </tr>
                                </form>
                            
                            </thead>
                            

                            <tbody>
                                @forelse ($inventory as $item)
                                <tr class="hover:bg-gray-50">
                                    <!-- Name + Image -->
                                    <td class="px-4 py-2 border" colspan="2" x-data="{ open: false }">
                                        <div class="flex items-center gap-4">
                                            @if ($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" class="rounded object-cover flex-shrink-0" style="width:80px;height:80px;min-width:80px;" @click="open = true">
                                            @else
                                                <div class="flex-shrink-0 rounded bg-gray-100" style="width:80px;height:80px;min-width:80px;"></div>
                                            @endif
                                            <div class="text-left">
                                                <div class="font-medium">{{ $item->name }}</div>
                                                @if ($item->category)
                                                    <div class="text-xs text-gray-500">{{ $item->category }}</div>
                                                @endif
                                                <div class="text-xs text-gray-400">item #{{ $item->item_id }}</div>
                                            </div>
                                        </div>

                                        <!-- Image Preview Modal -->
                                        <div x-show="open" x-cloak
                                             x-transition.opacity
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                             @click.self="open = false"
                                             @keydown.escape.window="open = false">

                                            @if ($item->image_path)
                                                <div class="bg-white rounded-lg shadow-xl p-4 max-w-2xl w-full mx-4">
                                                    <img src="{{ asset('storage/' . $item->image_path) }}"
                                                         class="w-full max-h-[70vh] object-contain rounded">
                                                </div>
                                            @endif
                                        </div>

                                    </td>

                                    <td class="px-4 py-2 border">{{ env('CURRENCY').number_format($item->price) }}</td>
                                    <td class="px-4 py-2 border truncate-text text-gray-400">{{ $item->description ?: '—' }}</td>

                                    <!-- AI Labels -->
                                    <td class="px-4 py-2 border whitespace-normal">
                                        @php
                                            $l = $item->labels;
                                            $chips = [];
                                            if ($l) {
                                                foreach (['course', 'cuisine', 'temperature', 'richness'] as $key) {
                                                    if (!empty($l[$key]) && $l[$key] !== 'unknown') {
                                                        $chips[] = [$key, str_replace('_', ' ', $l[$key])];
                                                    }
                                                }
                                                foreach (($l['flavor'] ?? []) as $flavor) {
                                                    $chips[] = ['flavor', $flavor];
                                                }
                                                foreach (($l['weather_fit'] ?? []) as $weather) {
                                                    if ($weather !== 'any') {
                                                        $chips[] = ['weather', $weather . ' day'];
                                                    }
                                                }
                                            }
                                            $chipClass = [
                                                'course' => 'bg-indigo-100 text-indigo-800',
                                                'cuisine' => 'bg-purple-100 text-purple-800',
                                                'temperature' => 'bg-sky-100 text-sky-800',
                                                'richness' => 'bg-gray-100 text-gray-700',
                                                'flavor' => 'bg-orange-100 text-orange-800',
                                                'weather' => 'bg-teal-100 text-teal-800',
                                            ];
                                        @endphp
                                        @if ($l)
                                            <div class="flex flex-wrap gap-1" title="{{ $l['summary_en'] ?? '' }}">
                                                @foreach ($chips as [$type, $text])
                                                    <span class="rounded-full px-2 py-0.5 text-xs {{ $chipClass[$type] }}">{{ $text }}</span>
                                                @endforeach
                                            </div>
                                            @if (!empty($l['summary_en']))
                                                <p class="mt-1 text-xs text-gray-500">{{ $l['summary_en'] }}</p>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Not labelled</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 border text-center"> {{ $item->is_active ? '🟢' : '🔴' }} </td>

                                    <td class="px-4 py-2 border"></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        No products found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ✅ Pagination (BOTTOM PAGE) -->
                    <div class="mt-6 flex flex-col md:flex-row justify-between gap-4">
                            {{ $inventory->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>