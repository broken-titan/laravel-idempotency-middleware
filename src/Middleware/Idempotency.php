<?php

    namespace BrokenTitan\Idempotency\Middleware;

    use Closure;
    use Illuminate\Contracts\Console\Kernel;
    use Illuminate\Http\{Request, Response};
    use Laravel\SerializableClosure\SerializableClosure;
     
    class Idempotency {
        public function handle(Request $request, Closure $next, ?string $header = null, ?string $method = null, ?int $expiration = null) {
            if (!in_array($request->method(), !empty($method) ? [$method] : config("idempotency.methods"))) {
                return $next($request);
            }

            $requestId = $request->header($header ?? config("idempotency.header"));
            if (!$requestId) {
                return $next($request);
            }
            $requestId = crc32($request->getContent()) . "-{$requestId}";

            if ($response = cache($requestId)) {
                return $response;
            }

            // Handle any closures that aren't Serializable.
			$next = new SerializableClosure($next);

            $response = $next($request);

            if ($response instanceof Response) {
                $responseToCache = new Response($response->content(), $response->status(), $response->headers->allPreserveCase());
                cache([$requestId => $responseToCache], now()->addMinutes($expiration ?? config("idempotency.expiration")));
            }

            return $response;
        }
    }
