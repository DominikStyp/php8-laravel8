<?php


namespace Tests;


abstract class ResourceControllerAbstract
    extends TestCase
    implements  ResourceControllerTestInterface {

    protected function jsonGet(string $uri): \stdClass {
        $response = $this->json("GET", $uri);
        return json_decode($response->content());
    }

    protected function jsonPost(string $uri, array $data): \stdClass {
        $response = $this->json("POST", $uri, $data);
        return json_decode($response->content());
    }

    protected function jsonPut(string $uri, array $data): \stdClass {
        $response = $this->json("PUT", $uri, $data);
        return json_decode($response->content());
    }

    protected function jsonPatch(string $uri, array $data): \stdClass {
        $response = $this->json("PATCH", $uri, $data);
        return json_decode($response->content());
    }

    protected function jsonDelete(string $uri): \stdClass {
        $response = $this->json("DELETE", $uri);
        return json_decode($response->content());
    }

}
