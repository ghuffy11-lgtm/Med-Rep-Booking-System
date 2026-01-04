<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAudit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log authenticated user actions
        if (auth()->check()) {
            // Log specific actions based on route and method
            $this->logAction($request, $response);
        }

        return $response;
    }

    /**
     * Log the action based on route and HTTP method
     */
    protected function logAction(Request $request, Response $response): void
    {
        // Only log successful actions (2xx responses)
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return;
        }

        $route = $request->route();
        if (!$route) {
            return;
        }

        $action = $this->determineAction($request);
        
        if ($action) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'auditable_type' => $this->getAuditableType($request),
                'auditable_id' => $this->getAuditableId($request),
                'action' => $action,
                'old_values' => null,
                'new_values' => $this->getRequestData($request),
                'metadata' => [
                    'route' => $route->getName(),
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                ],
                'ip_address' => $request->ip(),
            ]);
        }
    }

    /**
     * Determine the action type based on HTTP method and route
     */
    protected function determineAction(Request $request): ?string
    {
        $method = $request->method();
        $routeName = $request->route()->getName();

        // Map HTTP methods to action types
        $actionMap = [
            'POST' => 'created',
            'PUT' => 'updated',
            'PATCH' => 'updated',
            'DELETE' => 'deleted',
        ];

        // Special cases for specific routes
        if (str_contains($routeName, 'approve')) {
            return 'approved';
        }
        
        if (str_contains($routeName, 'reject')) {
            return 'rejected';
        }
        
        if (str_contains($routeName, 'cancel')) {
            return 'cancelled';
        }

        return $actionMap[$method] ?? null;
    }

    /**
     * Get the auditable type from the request
     */
    protected function getAuditableType(Request $request): string
    {
        $routeName = $request->route()->getName();

        // Map route names to model types
        if (str_contains($routeName, 'booking')) {
            return 'App\Models\Booking';
        }
        
        if (str_contains($routeName, 'department')) {
            return 'App\Models\Department';
        }
        
        if (str_contains($routeName, 'schedule')) {
            return 'App\Models\Schedule';
        }
        
        if (str_contains($routeName, 'user')) {
            return 'App\Models\User';
        }
        
        if (str_contains($routeName, 'config')) {
            return 'App\Models\GlobalSlotConfig';
        }

        return 'Unknown';
    }

    /**
     * Get the auditable ID from the request
     */
    protected function getAuditableId(Request $request): ?int
    {
        // Try to get ID from route parameters
        $route = $request->route();
        
        $possibleIdParams = ['id', 'booking', 'department', 'schedule', 'user'];
        
        foreach ($possibleIdParams as $param) {
            if ($route->hasParameter($param)) {
                $value = $route->parameter($param);
                return is_numeric($value) ? (int) $value : null;
            }
        }

        return null;
    }

    /**
     * Get sanitized request data for logging
     */
    protected function getRequestData(Request $request): array
    {
        $data = $request->except(['password', 'password_confirmation', '_token', '_method']);
        
        return array_filter($data, function($value) {
            return !is_null($value);
        });
    }
}
