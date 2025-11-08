<?php

namespace App\Http\Controllers\Front\Guide;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerJob;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class GuideFrontController extends Controller
{
    public function index()
    {
        return view('front.guide.index');
    }
    public function getHomePage(){
        $regions = Region::all();
        $categories = Category::query()
            ->where('parent_id', '>', 0)
            ->with(['parent', 'CustomerJobsByCategory'])
            ->withCount('CustomerJobsByCategory')
            ->has('CustomerJobsByCategory', '>', 1)     // only categories with > 1 related rows
            ->orderByDesc('customer_jobs_by_category_count')
            ->orderBy('priority')
            ->get();
        return view('front.guide.home' , compact('regions','categories'));
    }

    public function getCustomerPage($id){
        $region = View::shared('region');
        $customerJob = CustomerJob::findOrFail($id);
        if(empty($region) && !empty($customerJob)){
            $region = Region::find($customerJob->region_id);
        }

        return view('front.guide.customer-page',compact('customerJob','region'));
    }

    public function getSubCategoryPage(){
        return view('front.guide.sub-category');
    }

    public function showCategories(Request $request)
    {
        $region = Region::find($request->id);
        Cookie::queue('region_id', $region->id, 60 * 24 * 30); // Expires in 30 days
        $categories = Category::whereHas('CustomerJobsByParentCategory', function ($query) use ($region) {
            if(!empty($region)){
                $query->where('region_id', $region->id);
            }
        })->where('parent_id', 0)->with('children')->get();

//        dd(vsprintf(str_replace('?', '%s', $categories->toSql()),$categories->getBindings() ),$categories->get());

        $countCustomerJobs = CustomerJob::where('customer_jobs.region_id', $region->id)->count();
        if(empty($countCustomerJobs)){
            return view('front.guide.coming-soon',compact('region'));
        }

        return view('front.guide.category', compact('categories','region'));
    }

    public function showSubCategories ($name,$id)
    {
        $category = Category::find($id);
        $region = View::shared('region');
        $subCategories = Category::whereHas('CustomerJobsByCategory', function ($query) use ($region) {
            if(!empty($region)){
                $query->where('region_id', $region->id);
            }
        })
            ->where('parent_id', $id)->get();

//        dd(vsprintf(str_replace('?', '%s', $subCategories->toSql()),$subCategories->getBindings() ),$subCategories->get());

        return view('front.guide.sub-category',compact('category','subCategories'));
    }
    public function showCustomerJobs(request $request)
    {
        $category = Category::find($request->parent_id);
        $subCategory = Category::find($request->id);
        $region = View::shared('region');
        $customerJobs = CustomerJob::whereHas('subCategories', function ($query) use ($subCategory,$region) {
            $query->where('categories.id', $subCategory->id);
            if(!empty($region)){
                $query->where('customer_jobs.region_id', $region->id);
            }
        })->get();
        return view('front.guide.customer-jobs',compact('category','subCategory','customerJobs'));
    }
    public function showCustomer($id)
    {
        $region = View::shared('region');
        $customerJob = CustomerJob::findOrFail($id);
        if(empty($region) && !empty($customerJob)){
            $region = Region::find($customerJob->region_id);
        }
        return view('front.guide.customer',compact('customerJob','region'));
    }

    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $customerJobs = CustomerJob::whereHas('customer', function ($query) use ($searchQuery) {
            $query->where('name', 'LIKE', "%{$searchQuery}%")
                ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(customer_name, '$[0]'))) LIKE LOWER(?)", ["%{$searchQuery}%"])
             ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(customer_name, '$[1]'))) LIKE LOWER(?)", ["%{$searchQuery}%"]);
        })->get();

//        dd(vsprintf(str_replace('?', '%s', $customerJobs->toSql()),$customerJobs->getBindings() ),$customerJobs->get());
        return view('front.guide.search', compact('customerJobs'));
    }

}
