<?php

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows home feed with chirps', function () {
    $user = User::factory()->create();

    $user->chirps()->create(['message' => 'First chirp message']);
    $user->chirps()->create(['message' => 'Second chirp message']);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertViewIs('home');
    $response->assertViewHas('chirps');

    $response->assertSeeText('First chirp message');
    $response->assertSeeText('Second chirp message');
});

it('redirects guest to login when trying to store chirp', function () {
    $this->post('/chirps', [
        'message' => 'Hello world!',
    ])->assertRedirect('/login');
});

it('stores a chirp for authenticated user and redirects home with success', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/chirps', [
        'message' => 'Hello world!',
    ]);

    $response
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Your chirp has been posted!');

    $this->assertDatabaseHas('chirps', [
        'user_id' => $user->id,
        'message' => 'Hello world!',
    ]);
});

it('validates message is required on store', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/')
        ->post('/chirps', ['message' => ''])
        ->assertRedirect('/')
        ->assertSessionHasErrors(['message']);

    expect(Chirp::count())->toBe(0);
});

it('validates message min length 5 on store', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/')
        ->post('/chirps', ['message' => 'Hey!']) // 4 chars
        ->assertRedirect('/')
        ->assertSessionHasErrors(['message']);

    expect(Chirp::count())->toBe(0);
});

it('validates message max length 255 on store', function () {
    $user = User::factory()->create();
    $long = str_repeat('a', 256);

    $this->actingAs($user)
        ->from('/')
        ->post('/chirps', ['message' => $long])
        ->assertRedirect('/')
        ->assertSessionHasErrors(['message']);

    expect(Chirp::count())->toBe(0);
});

it('prevents duplicate message per same user (unique per user)', function () {
    $user = User::factory()->create();

    $user->chirps()->create(['message' => 'Same message']);

    $this->actingAs($user)
        ->from('/')
        ->post('/chirps', ['message' => 'Same message'])
        ->assertRedirect('/')
        ->assertSessionHasErrors(['message']);

    expect(
        Chirp::where('user_id', $user->id)->where('message', 'Same message')->count()
    )->toBe(1);
});

it('allows same message for different users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $userA->chirps()->create(['message' => 'Same message']);

    $this->actingAs($userB)
        ->post('/chirps', ['message' => 'Same message'])
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Your chirp has been posted!');

    expect(Chirp::where('message', 'Same message')->count())->toBe(2);
});

it('redirects guest to login when trying to update chirp', function () {
    $owner = User::factory()->create();
    $chirp = $owner->chirps()->create(['message' => 'Owner message']);

    $this->put("/chirps/{$chirp->id}", ['message' => 'New message'])
        ->assertRedirect('/login');
});

it('allows owner to update chirp', function () {
    $user = User::factory()->create();
    $chirp = $user->chirps()->create(['message' => 'Old message']);

    $this->actingAs($user)
        ->put("/chirps/{$chirp->id}", ['message' => 'New message'])
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Chirp updated!');

    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'New message',
    ]);
});

it('validates message on update', function () {
    $user = User::factory()->create();
    $chirp = $user->chirps()->create(['message' => 'Old message']);

    $this->actingAs($user)
        ->from('/')
        ->put("/chirps/{$chirp->id}", ['message' => ''])
        ->assertRedirect('/')
        ->assertSessionHasErrors(['message']);

    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'Old message',
    ]);
});

it('forbids updating chirp of another user (policy)', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $chirp = $owner->chirps()->create(['message' => 'Owner message']);

    $this->actingAs($intruder)
        ->put("/chirps/{$chirp->id}", ['message' => 'Hacked'])
        ->assertForbidden();

    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'Owner message',
    ]);
});

it('redirects guest to login when trying to delete chirp', function () {
    $owner = User::factory()->create();
    $chirp = $owner->chirps()->create(['message' => 'To delete']);

    $this->delete("/chirps/{$chirp->id}")
        ->assertRedirect('/login');
});

it('allows owner to delete chirp', function () {
    $user = User::factory()->create();
    $chirp = $user->chirps()->create(['message' => 'To delete']);

    $this->actingAs($user)
        ->delete("/chirps/{$chirp->id}")
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Chirp deleted!');

    $this->assertDatabaseMissing('chirps', [
        'id' => $chirp->id,
    ]);
});

it('forbids deleting chirp of another user (policy)', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $chirp = $owner->chirps()->create(['message' => 'Owner message']);

    $this->actingAs($intruder)
        ->delete("/chirps/{$chirp->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
    ]);
});
