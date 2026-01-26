<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Models\CustomerJob;
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

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function customerJobs(){
        $category_id = request()->category_id;
        $customerJobs = CustomerJob::query()
            ->select(['id', 'name', 'customer_id']) // only what you need
            ->whereHas('subCategories', fn ($q) => $q->where('category_id', $category_id))
            ->with([
                // only needed columns
                'customer:id,customer_name',
                // load only first image (Laravel 8.41+ / 9+)
                'images' => fn ($q) => $q->select(['id', 'customer_job_id', 'path'])->limit(1),
            ])
            ->get()
            ->map(function ($job) {
                $customerName = $job->customer?->customer_name;

                return [
                    'customer_job_id'   => $job->id,
                    'customer_job_name' => $job->name,
                    'customer_name'     => is_array($customerName)
                        ? implode(', ', $customerName)
                        : (string) $customerName,
                    'customer_job_image' => $job->images->first()
                        ? asset('storage',$job->images->first()->path)
                        : null,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $customerJobs,
        ]);
    }
    public function categories(): JsonResponse
    {

        $categories = Category::query()
            ->where('parent_id', '>', 0)
            ->withCount('CustomerJobsByCategory')
            ->has('CustomerJobsByCategory', '>', 1)
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->get();

        $data = [];
        foreach ($categories as $key=> $category) {
            $data[$key]['category_id'] = $category->id;
            $data[$key]['category_name'] = $category->getTranslation('name', 'en');
            $data[$key]['category_icon'] = $category->icon;
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }


    public function featuredCustomers(): JsonResponse
    {

        $data = [];
        $customerJobs = CustomerJob::with('images')->limit(5)->get();
        foreach($customerJobs as $key => $customerJob){
            $data[$key]['customer_name']= $customerJob->name;
            $data[$key]['permalink']= $customerJob->permalink;
            $data[$key]['images']= $customerJob->images;
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
