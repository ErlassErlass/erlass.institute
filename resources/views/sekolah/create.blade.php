    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Sekolah</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-md rounded">
                <form method="POST" action="{{ route('sekolah.store') }}">
                    @csrf

                    <!-- Kode Sekolah -->
                    <div class="mt-4">
                        <x-input-label for="kodlan" value="Kode Sekolah" />
                        <x-text-input id="kodlan" class="block mt-1 w-full" type="text" name="kodlan" :value="old('kodlan')" required autofocus />
                        @error('kodlan')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nama Sekolah -->
                    <div class="mt-4">
                        <x-input-label for="namasekolah" value="Nama Sekolah" />
                        <x-text-input id="namasekolah" class="block mt-1 w-full" type="text" name="namasekolah" :value="old('namasekolah')" required />
                        @error('namasekolah')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Rank -->
                    <div class="mt-4">
                        <x-input-label for="rank" value="Rank" />
                        <x-text-input id="rank" class="block mt-1 w-full" type="text" name="rank" :value="old('rank')" />
                        @error('rank')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Jenjang -->
                    <div class="mt-4">
    <x-input-label for="jenjang" value="Jenjang" />
    <select name="jenjang" id="jenjang" class="block mt-1 w-full border-gray-300 rounded-md">
        <option value="SD" {{ old('jenjang') == 'SD' ? 'selected' : '' }}>SD</option> <!-- Fixed missing quotes -->
        <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
    </select>
    @error('jenjang')
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>

                    <!-- Sub Jenjang -->
                    <div class="mt-4">
                        <x-input-label for="sub_jenjang" value="Sub Jenjang" />
                        <x-text-input id="sub_jenjang" class="block mt-1 w-full" type="text" name="sub_jenjang" :value="old('sub_jenjang')" />
                        @error('sub_jenjang')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
    <x-input-label for="status" value="Status" />
    <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md">
        <option value="Swasta" {{ old('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
        <option value="Negeri" {{ old('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
    </select>
    @error('status')
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>

                    <!-- PD -->
                    <div class="mt-4">
                        <x-input-label for="pd" value="PD" />
                        <x-text-input id="pd" class="block mt-1 w-full" type="text" name="pd" :value="old('pd')" />
                        @error('pd')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Kecamatan -->
                    <div class="mt-4">
                        <x-input-label for="kec" value="Kecamatan" />
                        <x-text-input id="kec" class="block mt-1 w-full" type="text" name="kec" :value="old('kec')" required />
                        @error('kec')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Kota/Kabupaten -->
                    <div class="mt-4">
                        <x-input-label for="kotkab" value="Kota/Kabupaten" />
                        <x-text-input id="kotkab" class="block mt-1 w-full" type="text" name="kotkab" :value="old('kotkab')" required />
                        @error('kotkab')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Kota -->
                    <div class="mt-4">
                        <x-input-label for="kota" value="Kota" />
                        <x-text-input id="kota" class="block mt-1 w-full" type="text" name="kota" :value="old('kota')" required />
                        @error('kota')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Provinsi -->
                    <div class="mt-4">
                        <x-input-label for="provinsi" value="Provinsi" />
                        <x-text-input id="provinsi" class="block mt-1 w-full" type="text" name="provinsi" :value="old('provinsi')" required />
                        @error('provinsi')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            Simpan Sekolah
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<html><body><h1>Test</h1></body></html>


