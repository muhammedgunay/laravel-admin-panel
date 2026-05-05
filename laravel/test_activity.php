<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$activity = Spatie\Activitylog\Models\Activity::latest()->first();
echo json_encode([
    'causer_id' => $activity->causer_id,
    'causer_type' => $activity->causer_type,
    'causer_name' => $activity->causer ? $activity->causer->name : null,
]);
