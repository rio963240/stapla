<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    Route::put('/settings/basic', function () {
        $user = request()->user();
        $input = [
            'name' => request()->input('name'),
            'email' => $user->email,
        ];
        if (request()->hasFile('photo')) {
            $input['photo'] = request()->file('photo');
        }

        app(\Laravel\Fortify\Contracts\UpdatesUserProfileInformation::class)
            ->update($user, $input);

        if (request()->expectsJson()) {
            $freshUser = $user->fresh();
            return response()->json([
                'status' => 'basic-info-updated',
                'name' => $freshUser?->name,
                'photo_url' => $freshUser?->profile_photo_url,
            ]);
        }

        return redirect()
            ->route('settings')
            ->with('status', 'basic-info-updated');
    })->name('settings.basic.update');

    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
});
