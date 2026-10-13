<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController
 *
 * Handles API requests and responses related to category management.
 *
 * @package App\Http\Controllers\Api\V1
 */
class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 10);
        $filters = $request->only(['search']);

        $categories = $this->categoryService->paginate($perPage, $filters);

        return $this->successResponse(
            data: CategoryResource::collection($categories->items()),
            meta: [
                'page' => $categories->currentPage(),
                'limit' => $categories->perPage(),
                'totalItems' => $categories->total(),
                'totalPages' => $categories->lastPage(),
            ],
            message: 'Categories data retrieved successfully.'
        );
    }

    /**
     * Get all category options for dropdown selections.
     *
     * @return JsonResponse
     */
    public function options(): JsonResponse
    {
        return $this->successResponse(
            data: CategoryResource::collection($this->categoryService->options())->resolve(),
            message: 'Category options retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return $this->successResponse(
            data: new CategoryResource($category),
            message: 'Category created successfully.',
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(
            data: new CategoryResource($category),
            message: 'Category data retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        return $this->successResponse(
            data: new CategoryResource($category),
            message: 'Category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return $this->successResponse(
            message: 'Category deleted successfully.'
        );
    }
}
