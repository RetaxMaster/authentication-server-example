<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_generation() {

        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->post("oauth/clients", [
            "name" => "Client Test",
            "redirect" => "http://example.com/callback"
        ]);

        $response
            ->assertJsonStructure([
                "id",
                "plainSecret",
                "user_id",
                "name",
                "redirect"
            ])
            ->assertStatus(200);

    }

    public function test_list_clients() {

        $user = User::factory()->create();
        Passport::actingAs($user);

        // Creo unos cuantos clientes para este usuario. La definición del factory se puede ver en vendor/laravel/passport/database/factories
        Client::factory(10)->create([
            "user_id" => $user->id
        ]);

        $response = $this->get("oauth/clients");

        $response
            ->assertJsonCount(10)
            ->assertStatus(200);

    }

    public function test_update_client() {

        $user = User::factory()->create();
        Passport::actingAs($user);

        $client = Client::factory()->create([
            "user_id" => $user->id
        ]);
        
        $response = $this->put("oauth/clients/$client->id", [
            "name" => "New Client Name",
            "redirect" => "http://127.0.0.1/callback"
        ]);

        $response->assertStatus(200);

        // Podemos ver qué nos devuelve $response con $response->dump();
        $this->assertEquals($response["name"], "New Client Name");
        $this->assertEquals($response["redirect"], "http://127.0.0.1/callback");
        
    }

    public function test_client_deletion() {

        $user = User::factory()->create();
        Passport::actingAs($user);

        $client = Client::factory()->create([
            "user_id" => $user->id
        ]);
        
        // Laravel NO lo elimina de la base de datos, sino que cambia la propiedad "revoked" de este cliente a 1 para indicar que ha sido removido (es un soft-delete)
        $this->delete("oauth/clients/$client->id");

        $this->assertDatabaseHas("oauth_clients", [
            "id" => $client->id,
            "revoked" => 1
        ]);
        
    }

}
