<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CategoryController
 * @group Category
 *
 * APIs to manage categories
 */
class CategoryController extends Controller
{
    /**
     *
     * @param Request $request
     * @return CategoryCollection
     * @apiResourceCollection App\Http\Resources\CategoryCollection
     * @apiResourceModel App\Models\Category
     */
    public function index(Request $request): CategoryCollection
    {
        $ctgQuery = Category::query();

        return $this->runIndex($request, $ctgQuery, CategoryCollection::class);
    }

    /**
     *
     * @param CategoryStoreRequest $request
     * @return CategoryResource
     * @apiResourceModel App\Models\Category
     */
    public function store(CategoryStoreRequest $request): CategoryResource
    {
        $category = Category::create($request->validated());

        return new CategoryResource($category);
    }

    /**
     *
     * @param Category $category
     * @return CategoryResource
     * @apiResourceModel App\Models\Category
     */
    public function show(String $id): CategoryResource
    {
        $ctg = Category::findOrFail($id);
        // dd($ctg);
        return new CategoryResource($ctg);
    }

    /**
     *
     * @param CategoryUpdateRequest $request
     * @param Category $category
     * @return CategoryResource
     * @apiResourceModel App\Models\Category
     */
    public function update(CategoryUpdateRequest $request, String $id): CategoryResource
    {
        $category = Category::findOrFail($id);
        $category->fill($request->validated())->save();

        return new CategoryResource($category);
    }

    /**
     *
     * @param Category $category
     * @return Response
     */
    public function destroy(String $id): Response
    {
        $category = Category::findOrFail($id);
        $category->delete();

        // Return a response indicating successful deletion
        return response()->noContent();
    }
}
