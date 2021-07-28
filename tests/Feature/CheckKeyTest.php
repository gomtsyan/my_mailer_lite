<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckKeyTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_key()
    {
        $response = $this->getJson('/api/v1/check/key?apiKey=8fc1b35530cd5e37f43942077c065c6f&sessionId=4ds6f54sd6f5sd4f6s4df56');

        $response->assertStatus(422);
    }
}
