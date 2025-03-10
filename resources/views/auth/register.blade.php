<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Nama Lengkap -->
    <div>
        <x-input-label for="nama_lengkap" value="Nama Lengkap" />
        <x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" :value="old('nama_lengkap')" required autofocus />
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" value="Password" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
        <x-input-label for="password_confirmation" value="Confirm Password" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
    </div>

    <!-- Tanggal Lahir -->
    <div class="mt-4">
        <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
        <x-text-input id="tanggal_lahir" class="block mt-1 w-full" type="date" name="tanggal_lahir" :value="old('tanggal_lahir')" required />
    </div>

    <!-- No. Telephone -->
    <div class="mt-4">
        <x-input-label for="no_telephone" value="No. Telephone" />
        <x-text-input id="no_telephone" class="block mt-1 w-full" type="text" name="no_telephone" :value="old('no_telephone')" required />
    </div>

    <!-- Agama -->
    <div class="mt-4">
        <x-input-label for="agama" value="Agama" />
        <x-text-input id="agama" class="block mt-1 w-full" type="text" name="agama" :value="old('agama')" required />
    </div>

    <!-- Pendidikan Terakhir -->
    <div class="mt-4">
        <x-input-label for="pend_terakhir" value="Pendidikan Terakhir" />
        <x-text-input id="pend_terakhir" class="block mt-1 w-full" type="text" name="pend_terakhir" :value="old('pend_terakhir')" required />
    </div>

    <!-- Kompetensi 1 -->
    <div class="mt-4">
        <x-input-label for="kompetensi_1" value="Kompetensi 1" />
        <x-text-input id="kompetensi_1" class="block mt-1 w-full" type="text" name="kompetensi_1" :value="old('kompetensi_1')" required />
    </div>

    <!-- Kompetensi 2 (Optional) -->
    <div class="mt-4">
        <x-input-label for="kompetensi_2" value="Kompetensi 2 (Opsional)" />
        <x-text-input id="kompetensi_2" class="block mt-1 w-full" type="text" name="kompetensi_2" :value="old('kompetensi_2')" />
    </div>

    <div class="flex items-center justify-end mt-4">
        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
            Already registered?
        </a>

        <x-primary-button class="ml-4">
            Register
        </x-primary-button>
    </div>
</form>