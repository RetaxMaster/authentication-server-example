<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
USE Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Tests\TestCase;

class TokenTest extends TestCase {

    use RefreshDatabase;

    public function test_request_token() {
        
        // Creo el usuario
        $user = User::factory()->create();
        
        // Creo el cliente de pruebas
        $client = Client::factory()->create([
            "password_client" => true
        ]);

        // Pido el token
        $response = $this->post("oauth/token", [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->plainSecret,
            'username' => $user->email,
            'password' => "password",
            'scope' => ''
        ]);

        $response
            ->assertJsonStructure([
                "token_type",
                "expires_in",
                "access_token",
                "refresh_token"
            ])
            ->assertStatus(200);

    }

    public function test_fail_request_token() {

        // Pido el token
        $response = $this->post("oauth/token", [
            'grant_type' => 'password',
            'client_id' => "wrong_client_id",
            'client_secret' => "wrong_client_secret",
            'username' => "wrong_username",
            'password' => "wrong_password",
            'scope' => ''
        ]);

        $response
            ->assertJsonStructure([
                "error",
                "error_description",
                "message"
            ])
            ->assertStatus(401);

    }

    public function test_refresh_token() {

        // Creo el usuario
        $user = User::factory()->create();
        
        // Creo el cliente de pruebas
        $client = Client::factory()->create([
            "password_client" => true
        ]);

        // Pido el token
        $token = $this->post("oauth/token", [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->plainSecret,
            'username' => $user->email,
            'password' => "password",
            'scope' => ''
        ]);

        $response = $this->post("oauth/token", [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token["refresh_token"],
            'client_id' => $client->id,
            'client_secret' => $client->plainSecret,
            'scope' => ''
        ]);

        $response
            ->assertJsonStructure([
                "token_type",
                "expires_in",
                "access_token",
                "refresh_token"
            ])
            ->assertStatus(200);
        
    }
    
    /* Esta forma no funciona porque por alguna razón, el client que refresa el JSON API no es válido para el grant_type password (o al menos esa es la teoría), sería bueno saber por qué... otra teoría es que, el password grant flow es unicamente para aplicaciones de confianza, por lo que no estaría bien que se permitan generar clientes para este flujo con un simple llamado a la API, sino que se deben generar manualmente para las pocas aplicaciones de confianza que tienes...  UPDATE: la teoría es correcta

    public function test_request_token() {

        // Creo el usuario
        $user = User::factory()->create();

        Passport::actingAs($user);

        // Creo las credenciales
        $credentials = $this->post("oauth/clients", [
            "name" => "Test app",
            "redirect" => "http://127.0.0.1:8000/callback"
        ]);

        // Pido el token
        $data = [
            'grant_type' => 'password',
            'client_id' => $credentials["id"],
            'client_secret' => $credentials["plainSecret"],
            'username' => $user->email,
            'password' => Hash::make("password"),
            'scope' => ''
        ];

        dd($data);

        $response = $this->post("oauth/token", $data);

        dd($response);

        $response->assertStatus(200);

    } */
    
}
