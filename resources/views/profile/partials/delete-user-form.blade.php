<section>
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Warning:</strong> Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.
    </div>

    <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
        @csrf
        @method('delete')

        <div class="mb-3">
            <x-input-label for="password" :value="__('Enter your password to confirm deletion')" />
            <x-text-input 
                id="password" 
                name="password" 
                type="password" 
                class="form-control" 
                placeholder="Your current password"
                required 
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="text-danger mt-1" />
        </div>

        <x-danger-button type="submit">
            <i class="bi bi-trash me-2"></i>{{ __('Delete Account') }}
        </x-danger-button>
    </form>
</section>
