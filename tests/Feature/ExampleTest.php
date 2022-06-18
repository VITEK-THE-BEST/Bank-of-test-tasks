<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\Bank;
use Faker\Provider\ru_RU as Faker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    /**
     * тестирование создания банка.
     *
     * @return void
     */
    public function testCreate()
    {
        $token = $this->getUserToken();

        $faker = Faker\Company::companyNameElement();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/bank/create', [
            'name' => $faker,
            'credits' => 2
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['name' => $faker]);
        $this->assertDatabaseHas('banks', [
            'name' => $faker,
            'credits' => 2
        ]);


        //валидация
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/bank/create', [
        ]);
        $this->assertEquals(302, $response->status());
    }

    public function getUserToken()
    {
        $email = STR::random(30) . '@gmail.com';
        $pwd = STR::random(30);

        $response = $this->post('api/registration',[
            'first_name' => Faker\Person::firstNameFemale(),
            'last_name' => Faker\Person::firstNameFemale(),
            'patronymic' => Faker\Person::firstNameFemale(),
            'email' => $email,
            'password' => $pwd,
        ]);


        return $response['token'];
    }
}
