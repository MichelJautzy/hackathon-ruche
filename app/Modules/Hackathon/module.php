<?php

use App\Modules\Auth\Models\User;
use App\Modules\Hackathon\Controllers\HackathonController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;

return [
    'name' => 'Hackathon',

    /**
     * Les routes peuvent être définies ici ou directement dans les Actions.
     */
    'routes' => [
        'web' => function () {
            Route::get('/hackathon/barcode', [HackathonController::class, 'barcode'])->name('hackathon.barcode');
            Route::get('/hackathon/schema', [HackathonController::class, 'schema'])->name('hackathon.schema');
        },
        'api' => function () {
            // Route::get(...);
        },
    ],

    /**
     * Les features Laravel Pennant peuvent être activées/désactivées/configurées ici selon différentes conditions (comme l'utilisateur).
     * En Vue, il est possible d'obtenir la valeur d'une feature avec 'features.example' ou 'features['example']'.
     */
    'features' => [
        // 'example' => fn (User $user) => true,
    ],

    /**
     * Les commandes peuvent être planifiées ici, comme dans le fichier Kernel.php.
     */
    'schedule' => function (Schedule $schedule) {
        // $schedule->command('inspire')->daily();
    },

    /**
     * Les commandes sont ajoutées ici et/ou dans un sous-dossier `Commands` (à créer).
     * À noter que les Actions avec 'asCommand' sont également automatiquement enregistrées.
     */
    'commands' => function () {
        // Artisan::command(...);
    },

    /**
     * Channels pour le broadcasting.
     */
    'channels' => function () {
        // Broadcast::channel(...);
    },
];
