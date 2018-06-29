<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IpTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->json('POST', '/search', ['ip' => '']);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message'
            ])
            ->assertJson([
                'code' => "1001",
                'message'=> '未提交ip',
            ]);


    }
}
