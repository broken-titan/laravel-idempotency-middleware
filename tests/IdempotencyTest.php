<?php

    namespace Tests;

    use BrokenTitan\Idempotency\Middleware\Idempotency;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\{Cache, Config};
    use \Orchestra\Testbench\TestCase;

    class IdempotencyTest extends TestCase {
        public function setUp() : void {
            parent::setUp();

            Config::set("idempotency.methods", ["POST"]);
            Config::set("idempotency.header", "Idempotency-Key");
            Config::set("idempotency.expiration", 1440);
        }

        public function test_IdempotentMethod_CachesResponse() {
            $requestId = "request-test";

            $request = Request::create("/", "POST", [], [], [], [], "Test body");
            $request->headers->set(Config::get("idempotency.header"), $requestId); 

            $response = (new Idempotency)->handle($request, fn($request) => "next");

            $this->assertEquals("next", Cache::get(crc32($request->getContent()) . "-{$requestId}"));
            $this->assertEquals("next", $response);
        }

        public function test_IdempotentMethodWithDifferentChecksum_CachesResponse() {
            $requestId = "request-test";

            $request = Request::create("/", "POST", [], [], [], [], "Test body");
            $request->headers->set(Config::get("idempotency.header"), $requestId); 

            $response = (new Idempotency)->handle($request, fn($request) => "next");

            $this->assertEquals("next", Cache::get(crc32($request->getContent()) . "-{$requestId}"));
            $this->assertEquals("next", $response);

            $request = Request::create("/", "POST", [], [], [], [], "Test body 2");
            $request->headers->set(Config::get("idempotency.header"), $requestId); 

            $response = (new Idempotency)->handle($request, fn($request) => "next2");

            $this->assertEquals("next2", Cache::get(crc32($request->getContent()) . "-{$requestId}"));
            $this->assertEquals("next2", $response);
        }

        public function test_NonIdempotentMethod_DoesNotCacheResponse() {
            $requestId = "request-test";

            $request = Request::create("/", "GET");
            $request->headers->set(Config::get("idempotency.header"), $requestId); 

            $response = (new Idempotency)->handle($request, fn($request) => "next");

            $this->assertEmpty(Cache::get(crc32($request->getContent()) . "-{$requestId}"));
            $this->assertEquals("next", $response);
        }

        public function test_NonIdempotentHeader_DoesNotCacheResponse() {
            $requestId = "request-test";

            $request = Request::create("/", "POST");

            $response = (new Idempotency)->handle($request, fn($request) => "next");

            $this->assertEmpty(Cache::get(crc32($request->getContent()) . "-{$requestId}"));
            $this->assertEquals("next", $response);
        }
    }