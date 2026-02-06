<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Profil</h1>
            <p class="text-muted mb-0">Mettre a jour vos informations et mot de passe.</p>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-soft p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-soft p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</x-app-layout>
