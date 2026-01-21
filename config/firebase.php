<?php

declare(strict_types=1);

return [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/firebase.json'),
];

