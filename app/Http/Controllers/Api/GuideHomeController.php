<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class GuideHomeController extends Controller
{
    public function home(): JsonResponse
    {
        $categories = Category::select('categories.id', 'categories.name', DB::raw("CONCAT('https://alpha.kjshoplb.com/storage/', categories.logo) as logo"))
            ->where('parent_id', '>', 0)
            ->with([
                'parent',
                'CustomerJobsByCategory',
            ])
            ->withCount('CustomerJobsByCategory')
            ->has('CustomerJobsByCategory', '>', 1)
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->limit(2)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }
}
