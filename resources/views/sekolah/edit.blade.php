<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sekolah</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-md rounded">
                <form action="{{ route('sekolah.update', $sekolah) }}" method="POST">
                    @csrf @method('PUT')

                    <!-- Include all fields from the create form -->
                    <div class="mb-4">
                        <x-label for="kodlan" value="Kode Sekolah" />
                        <x-input id="kodlan" class="block mt-1 w-full" type="text" name="kodlan" value="{{ $sekolah->kodlan }}" readonly />
                    </div>

                    <div class="mb-4">
                        <x-label for="namasekolah" value="Nama Sekolah" />
                        <x-input id="namasekolah" class="block mt-1 w-full" type="text" name="namasekolah" value="{{ $sekolah->namasekolah }}" required />
                    </div>

                    <!-- Repeat for other fields... -->
                    
                    <div class="flex items-center justify-end">
                        <x-button class="ml-4">
                            Perbarui
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>