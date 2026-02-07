<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerJob;
use App\Models\Region;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GuideHomeController extends Controller
{
    public function home(): JsonResponse
    {
        $categories = Category::query()
            ->where('parent_id', '>', 0)
            ->with([
                'parent',
                'customerJobsByCategory',
            ])
            ->withCount('customerJobsByCategory')
            ->has('customerJobsByCategory', '>', 1)
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->get();

        $data = [];
        foreach ($categories as $key=> $category) {
            $data[$key]['category_name'] = $category->name;
            $data[$key]['category_logo'] = $category->logo;
            foreach($category->customerJobsByCategory as $customer_key => $customerJobsByCategory){
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
        $region_id = request()->region_id;

        // Get data
        $customerJobs = CustomerJob::query()
            ->select(['id', 'name', 'customer_id'])
            ->when($category_id, fn ($q) => $q->whereHas('subCategories', fn ($q) => $q->where('category_id', $category_id)))
            ->when($region_id, fn ($q) => $q->where('region_id', $region_id))
            ->with([
                'customer:id,customer_name',
                'firstImage:id,customer_job_id,path', // Load only first image per job
            ])
            ->get();

        // Map data
        $transformedData = $customerJobs->map(function ($job) {
            $customerName = $job->customer?->customer_name;

            return [
                'customer_job_id'   => $job->id,
                'customer_job_name' => $job->name,
                'customer_name'     => is_array($customerName)
                    ? implode(', ', $customerName)
                    : (string) $customerName,
                'customer_job_image' => $job->firstImage
                    ? asset('storage/' . ltrim($job->firstImage->path, '/'))
                    : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $transformedData,
        ]);
    }


    public function customerJob($customer_job_id){

        // Get data
        $customerJob = CustomerJob::query()
            ->select(['id', 'name', 'customer_id','mobile'])
            ->with([
                'customer:id,customer_name',
                'images:id,customer_job_id,path,image_type',
            ])
            ->where('id', $customer_job_id)
            ->first();

        // Handle not found
        if (!$customerJob) {
            return response()->json([
                'status' => false,
                'message' => 'Customer job not found',
            ], 404);
        }

        // Map data
        $customerName = $customerJob->customer?->customer_name;
        $customerJob_images = [];

        foreach($customerJob->images as $image){
            $customerJob_images[] = [ // Fixed: Changed to append array instead of overwriting
                'id' => $image->id,
                'path' => $image->path,
                'image_type' => $image->image_type,
            ];
        }

        $transformedData = [
            'customer_job_id'   => $customerJob->id,
            'customer_job_name' => $customerJob->name,
            'customer_job_mobile' => $customerJob->mobile,
            'customer_name'     => is_array($customerName)
                ? implode(', ', $customerName)
                : (string) $customerName,
            'customer_job_images' => $customerJob_images,
        ];

        return response()->json([
            'status' => true,
            'data'   => $transformedData,
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $filterByCustomerJobs = $request->boolean('customer_jobs_by_category');
        $region_id = $request->integer('region_id');
        $category_id = $request->integer('category_id');

        $parents = Category::query()
            ->where('parent_id', 0)
            ->orderBy('name')
            ->where('id',$category_id)
            ->get(['id', 'name', 'icon']);

        $children = Category::query()
            ->where('parent_id', '>', 0)
            ->when($filterByCustomerJobs, fn ($q) => $q->has('customerJobsByCategory')->withCount('customerJobsByCategory'))
            ->when($region_id, fn ($q) => $q->whereHas('customerJobsByCategory', fn ($q) => $q->where('region_id', $region_id)))
            ->where('parent_id',$category_id)
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'icon'])
            ->groupBy('parent_id');

        // Load region data only if region_id is provided
        $region = null;
        if ($region_id) {
            $region = Region::find($region_id);

            // Handle region not found
            if (!$region) {
                return response()->json([
                    'status' => false,
                    'message' => 'Region not found',
                ], 404);
            }
        }

        $categories = $parents->map(function ($parent) use ($children) {
            return [
                'category_id'   => $parent->id,
                'category_name' => $parent->getTranslation('name', 'en'),
                'category_icon' => $parent->icon,
                'children'      => ($children[$parent->id] ?? collect())
                    ->map(fn ($c) => [
                        'category_id'   => $c->id,
                        'category_name' => $c->getTranslation('name', 'en'),
                        'category_icon' => $c->icon,
                        'parent_id'     => $c->parent_id,
                    ])
                    ->values(),
            ];
        })->values();

        // Build response data
        $data = [
            'categories' => $categories,
        ];

        // Add region data if region exists
        if ($region) {
            $data['region'] = [
                'region_id'    => $region->id,
                'region_name'  => $region->getTranslation('name', 'en'),
                'region_image' => asset('storage/' . ltrim($region->path, '/')),
            ];
        }

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }
    public function topCategories(Request $request): JsonResponse
    {
        $parentId = $request->integer('parent_id'); // null if not provided

        $data = Category::query()
            ->select(['id', 'name', 'icon', 'priority', 'parent_id'])
            ->having('customer_jobs_by_category_count', '>', 0)
//            ->has('customerJobsByCategory', '>', 1)
            ->where('parent_id', $parentId)
            ->withCount('customerJobsByCategory')
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->get()
            ->map(fn ($category) => [
                'category_id'   => $category->id,
                'category_name' => $category->getTranslation('name', 'en'),
                'category_icon' => $category->icon,
            ])
            ->values();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }


    public function featuredStores(): JsonResponse
    {

        $data = [];
        $ids = [50, 31, 12, 46, 17];
        $customerJobs = CustomerJob::
            with([
                'firstImage:id,customer_job_id,path', // Load only first image per job
            ])
            ->whereIn('id', $ids)
                ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
                ->get();
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


    public function regions(): JsonResponse
    {
        $with_customers = request()->with_customers;

        $regions = Region::query()
            ->when($with_customers, function ($query) {
                $query->withCount('customer');
            })
            ->get();

        $data = $regions->map(function ($region) use ($with_customers) {
            $regionData = [
                'region_id'    => $region->id,
                'region_name'  => $region->getTranslation('name', 'en'),
                'region_image' => asset('storage/' . ltrim($region->path, '/')),
            ];

            if ($with_customers) {
                $regionData['customer_count'] = $region->customer_count;
            }

            return $regionData;
        });

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }


    public function customers(): JsonResponse
    {
        $region_id = request()->region_id;

        $customers = Customer::query()
            ->when($region_id, fn ($q) => $q->where('region_id', $region_id))
            ->get();

        $data = $customers->map(function ($customer) {
            $mobile = $customer->mobile;
            $customer_name = $customer->customer_name;

            return [
                'customer_id'     => $customer->id,
                'customer_name'   => is_array($customer_name)
                    ? implode(', ', $customer_name)
                    : (string) $customer_name,
                'customer_mobile' => is_array($mobile)
                    ? implode(', ', $mobile)
                    : (string) $mobile,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }
}
