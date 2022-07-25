<?php

    namespace BrokenTitan\Idempotency\Providers;

    use Illuminate\Support\ServiceProvider;

    class IdempotencyServiceProvider extends ServiceProvider {
        public function boot() {
            $this->mergeConfigFrom(__DIR__ . "/../../config/idempotency.php", "idempotency");
        }
    }
