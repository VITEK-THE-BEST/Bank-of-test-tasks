<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_QuestionsController()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
