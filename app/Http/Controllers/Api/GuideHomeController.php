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
        $categories = Category::query()
            ->where('parent_id', '>', 0)
            ->with([
                'parent',
                'CustomerJobsByCategory',
            ])
            ->withCount('CustomerJobsByCategory')
            ->has('CustomerJobsByCategory', '>', 1)
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->get();

        $data = [];
        foreach ($categories as $key=> $category) {
            $data[$key]['category_name'] = $category->name;
            $data[$key]['category_logo'] = $category->logo;
            foreach($category->CustomerJobsByCategory as $customer_key => $customerJobsByCategory){
                $data[$key]['customers'][$customer_key]['customer_name']= $customerJobsByCategory->name;
                $data[$key]['customers'][$customer_key]['permalink']= $customerJobsByCategory->permalink;
            }
        }
        dd($data,$category->CustomerJobsByCategory);

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }
}
