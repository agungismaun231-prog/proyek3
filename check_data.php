<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::all();
$customers = \App\Models\Customer::all();

echo "=== USERS ===\n";
foreach ($users as $user) {
    echo "{$user->id}: {$user->email} (role: {$user->role})\n";
}

echo "\n=== CUSTOMERS ===\n";
foreach ($customers as $customer) {
    echo "{$customer->id}: {$customer->email} ({$customer->name})\n";
}

echo "\n=== MISSING CUSTOMER RECORDS ===\n";
foreach ($users as $user) {
    if ($user->role === 'customer') {
        $customer = \App\Models\Customer::where('email', $user->email)->first();
        if (!$customer) {
            echo "MISSING: {$user->email}\n";
        }
    }
}
?>
