<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    use ApiResponse;
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(DisplayRequest $request)
    {
        $category = $this->categoryService->getAll($request->all());

        if ($category->isEmpty()) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Categories']));
        }

        return $this->responseSuccess('Categories retrieved successfully', CategoryResource::collection($category));
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryService->create($validated);

        return $this->responseSuccess(__('messages.created', ['module' => 'Category']), new CategoryResource($category));
    }

    public function update(CategoryRequest $request, $id)
    {
        $validated = $request->validated();
        $category = $this->categoryService->update($id, $validated);


        return $this->responseSuccess(__('messages.updated', ['module' => 'Category']), new CategoryResource($category));
    }
    public function destroy($id)
    {
        $category = $this->categoryService->toggleArchived($id);


        if (!$category) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'User']));
        }

        $message = $category->trashed()
            ? __('messages.archived', ['module' => 'Category'])
            : __('messages.restored', ['module' => 'Category']);


        return $this->responseSuccess($message, new CategoryResource($category));
    }
}
