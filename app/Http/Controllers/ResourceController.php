<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resource library endpoints backed by the MySQL connection (MYSQL_* in .env).
 */
class ResourceController extends Controller
{
    private const CONNECTION = 'mysql';

    private const TABLE = 'manual_files_copy';

    /**
     * GET /api/resources/types
     *
     * Returns the distinct resource types.
     */
    public function types(): JsonResponse
    {
        try {
            $types = DB::connection(self::CONNECTION)
                ->table(self::TABLE)
                ->distinct()
                ->orderBy('type')
                ->pluck('type');

            return $this->successResponse($types);
        } catch (Throwable $e) {
            Log::error('resource types failed', ['error' => $e->getMessage()]);

            return $this->errorResponse('Unable to fetch resource types.');
        }
    }

    /**
     * GET /api/resources/{type}/categories
     *
     * Returns the distinct categories for a type.
     */
    public function categories(string $type): JsonResponse
    {
        try {
            $categories = DB::connection(self::CONNECTION)
                ->table(self::TABLE)
                ->where('type', $type)
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            return $this->successResponse($categories);
        } catch (Throwable $e) {
            Log::error('resource categories failed', ['type' => $type, 'error' => $e->getMessage()]);

            return $this->errorResponse('Unable to fetch resource categories.');
        }
    }

    /**
     * GET /api/resources/{type}/{category}
     *
     * Returns the files for a type and category, newest first.
     */
    public function files(string $type, string $category): JsonResponse
    {
        try {
            $files = DB::connection(self::CONNECTION)
                ->table(self::TABLE)
                ->where('type', $type)
                ->where('category', $category)
                ->select('id', 'title', 'datetime', 'start_date', 'end_date', 'keywords', 'url')
                ->orderByDesc('datetime')
                ->orderByDesc('id')
                ->get();

            return $this->successResponse($files);
        } catch (Throwable $e) {
            Log::error('resource files failed', ['type' => $type, 'category' => $category, 'error' => $e->getMessage()]);

            return $this->errorResponse('Unable to fetch resources.');
        }
    }

    private function successResponse(iterable $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    private function errorResponse(string $message, int $status = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
