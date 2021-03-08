<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterUserTest extends TestCase {

    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_user() {
        
        $response = $this->post("api/register", [
            "email" => "user@test.com",
            "password" => "123456",
            "password_confirmation" => "123456"
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                "email" => "user@test.com"
            ]);

        $this->assertDatabaseHas("users", ["email" => "user@test.com"]);

    }

    public function test_more_than_one_user() {

        // Creo el primer usuario
        $this->post("api/register", [
            "email" => "user@test.com",
            "password" => "123456",
            "password_confirmation" => "123456"
        ]);

        // Vuelvo a crear el mismo usuario para provocar el error
        $response = $this->post("api/register", [
            "email" => "user@test.com",
            "password" => "123456",
            "password_confirmation" => "123456"
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                "status" => false,
                "message" => "El usuario ya existe"
            ]);
        
    }

}
