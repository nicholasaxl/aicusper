<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Header -->
                    <div>
                        <h3 class="text-lg font-semibold">Edit Product</h3>
                        <p class="text-sm text-gray-500">
                            Edit an integractive products to select on AI personalition to customers.</p>
                    </div>

                    <form method="POST"
                          action="{{ route('product.update', $inventory->inventory_id) }}"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf

                        <!-- Outlet -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Outlet
                            </label>
                            <select name="outlet_id" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="" selected disabled>Select</option>
                                @foreach ($outlets as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $inventory->outlet_id ? 'selected' : '' }} >{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('outlet_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Product Name
                            </label>
                            <input type="text"
                                   name="item_name"
                                   value="{{ $inventory->item_name }}"
                                   required
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('item_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Product Image
                            </label>

                            <div
                                x-data="{
                                    preview: '{{ $inventory->image_path ? asset('storage/'.$inventory->image_path) : '' }}'
                                }"
                                class="w-full"
                            >
                                <label
                                    class="flex items-center justify-center w-full px-4 py-6 border-2 border-dashed rounded-lg cursor-pointer
                                        border-gray-300 bg-gray-50 hover:bg-gray-100 transition"
                                >

                                    <!-- IMAGE PREVIEW -->
                                    <template x-if="preview">
                                        <img
                                            :src="preview"
                                            class="rounded max-h-40 object-contain"
                                        >
                                    </template>

                                    <!-- PLACEHOLDER -->
                                    <template x-if="!preview">
                                        <div class="text-center">
                                            <svg class="mx-auto h-10 w-10 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16V4m0 0l-4 4m4-4l4 4m6 4v8m0 0l-4-4m4 4l4-4"/>
                                            </svg>

                                            <p class="mt-2 text-sm text-gray-600">
                                                <span class="font-medium text-indigo-600">
                                                    Click to upload
                                                </span>
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                PNG, JPG up to 2MB
                                            </p>
                                        </div>
                                    </template>

                                    <!-- FILE INPUT -->
                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/*"
                                        class="hidden"
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                preview = URL.createObjectURL(file);
                                            }
                                        "
                                    >
                                </label>
                            </div>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Price
                            </label>
                            <input type="number"
                                   name="price"
                                   step="0.01"
                                   min="0"
                                   value="{{ (int)$inventory->price }}"
                                   required
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('price')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea name="description"
                                      rows="4"
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ $inventory->description }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <select name="status" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" {{ $inventory->active == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $inventory->active != 'active' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('product.index') }}"
                               class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                                Cancel
                            </a>

                            <x-primary-button class="ms-3">
                                {{ __(' Update Product') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>