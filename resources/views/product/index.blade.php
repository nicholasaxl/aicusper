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
                                List of product available to performed as selected on AI personalization.
                            </p>
                        </div>

                        <a href="{{ route('product.create') }}">
                            <x-primary-button>
                                {{ __('Add Product') }}
                            </x-primary-button>
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-fixed min-w-[900px] w-full border border-gray-200 whitespace-nowrap">
                            <colgroup>
                                <col style="width: 40%">
                                <col style="width: 0%">
                                <col style="width: 15%">
                                <col style="width: 25%">
                                <col style="width: 10%">
                                <col style="width: 10%">
                            </colgroup>
                            
                            <thead class="bg-gray-100">

                                <!-- Column Header -->
                                <tr>
                                    <th class="px-4 py-2 border text-start" colspan="2">Name</th>
                                    <th class="px-4 py-2 border text-start">Price</th>
                                    <th class="px-4 py-2 border text-start">Description</th>
                                    <th class="px-4 py-2 border text-center">Active</th>
                                    <th class="px-4 py-2 border text-center">Action</th>
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
                                            <img src="{{ asset('storage/' . $item->image_path) }}" class="rounded" width="100px" @click="open = true">
                                            <span class="font-medium text-left">
                                                {{ $item->item_name }}
                                            </span>
                                        </div>

                                        <!-- Image Preview Modal -->
                                        <div x-show="open"
                                             x-transition.opacity
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                             @click.self="open = false"
                                             @keydown.escape.window="open = false">

                                            <div class="bg-white rounded-lg shadow-xl p-4 max-w-2xl w-full mx-4">
                                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                                     class="w-full max-h-[70vh] object-contain rounded">
                                            </div>
                                        </div>

                                    </td>

                                    <td class="px-4 py-2 border">{{ env('CURRENCY').number_format($item->price) }}</td>
                                    <td class="px-4 py-2 border truncate-text ">{{ $item->description }}</td>
                                    <td class="px-4 py-2 border text-center"> {{ $item->active == 'active' ? '🟢' : '🔴' }} </td>

                                    <td class="px-4 py-2 border text-center space-x-2">                                       
                                        <a href="{{ route('product.edit', $item->inventory_id) }}"
                                            class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-indigo-600 hover:bg-gray-100 rounded">
                                             <!-- Pencil Icon (Heroicons style) -->
                                             <svg xmlns="http://www.w3.org/2000/svg"
                                                  class="w-5 h-5"
                                                  fill="none"
                                                  viewBox="0 0 24 24"
                                                  stroke="currentColor">
                                                 <path stroke-linecap="round"
                                                       stroke-linejoin="round"
                                                       stroke-width="2"
                                                       d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                             </svg>
                                         </a>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
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