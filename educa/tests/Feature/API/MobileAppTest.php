<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MobileAppTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMobileLogin()
    {
        $response = $this->post('/api/v1/loginReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
              "email" => "support@digitallearning.gmbh",
              "password" => "educaeduca321",
            ]
        );

        $response->assertStatus(200);
        self::assertNotNull($response->json("payload")["token"]);
    }

    public function testMobileLoginFailure()
    {
        $response = $this->post('/api/v1/loginReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "email" => "test",
                "password" => "testdsadsdasd",
            ]
        );

        $response->assertStatus(401);
        self::assertNull($response->json("payload"));
    }

    public function testMobileLogout()
    {

        $response = $this->post('/api/v1/loginReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "email" => "support@digitallearning.gmbh",
                "password" => "educaeduca321",
            ]
        );

        $response->assertStatus(200);
        self::assertNotNull($response->json("payload")["token"]);

        $token = $response->json("token");

        // Logout
        $response = $this->post('/api/v1/logoutReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "token" => $token,
            ]
        );

        $response->assertStatus(200);
        self::assertEquals("1",$response->json("status"));
    }
}
