<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\Feature\API\APITestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactAppTest extends APITestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMobileLogin()
    {
        $response = parent::createLoginResponse();
        $response->assertStatus(200);
        self::assertNotNull($response->json("status"));
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
        self::assertEquals("-1",$response->json("status"));
    }

    public function testMobileLogout()
    {
        $token = parent::getToken();
        // Logout
        $response = $this->post('/api/v1/logoutReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "token" => $token
            ]
        );

        $response->assertStatus(200);
        self::assertEquals("1",$response->json("status"));
    }

    public function testMeRoute()
    {
        $token = parent::getToken();

        $response = $this->call('GET','/api/v1/me',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "token" => $token
            ]
        );

        $response->assertStatus(200);
        self::assertNotNull($response->json("status"));
        self::assertNotNull($response->json("payload"));
        self::assertNotNull($response->json("payload")["user"]);
        self::assertNotNull($response->json("payload")["user"]["apps"]);
        self::assertNotNull($response->json("payload")["user"]["groups"]);


        self::assertEquals("Digital Learning Tester",$response->json("payload")["user"]["name"]);
        self::assertIsArray($response->json("payload")["user"]["apps"]);
    }
}
