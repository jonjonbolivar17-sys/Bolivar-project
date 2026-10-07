<?php

use App\Models\User;

test('admin can view user list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($admin)
        ->get('/admin/users');

    $response->assertOk();
});
