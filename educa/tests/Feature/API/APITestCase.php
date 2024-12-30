<?php


namespace Tests\Feature\API;


use Tests\TestCase;

class APITestCase extends TestCase
{

    function createLoginResponse($username = "support@digitallearning.gmbh", $password = "educaeduca321")
    {
        return $this->post('/api/v1/loginReact',
            ["security_token" => "9EuKHu5rVnQVqCYc72BdJTK9ptHMXNbFfkr8CQ94",
                "email" => $username,
                "password" => $password,
            ]
        );
    }

    function getToken($username = "support@digitallearning.gmbh", $password = "educaeduca321") {
        $response = $this->createLoginResponse($username, $password);
        self::assertEquals(200, $response->getStatusCode());
        self::assertNotNull($response->json("payload"));
        self::assertNotNull($response->json("payload")["token"]);
        return $response->json("payload")["token"];
    }
}
