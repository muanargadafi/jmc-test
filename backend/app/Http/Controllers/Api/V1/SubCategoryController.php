<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use App\Services\SubCategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SubCategoryController
 *
 * Handles API requests and responses related to subcategory management.
 *
 * @package App\Http\Controllers\Api\V1
 */
class SubCategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @param SubCategoryService $subCategoryService
     */
    public function __construct(private readonly SubCategoryService $subCategoryService)
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

        $subCategories = $this->subCategoryService->paginate($perPage, $filters);

        return $this->successResponse(
            data: SubCategoryResource::collection($subCategories->items()),
            meta: [
                'current_page' => $subCategories->currentPage(),
                'last_page' => $subCategories->lastPage(),
                'per_page' => $subCategories->perPage(),
                'total' => $subCategories->total(),

                'page' => $subCategories->currentPage(),
                'limit' => $subCategories->perPage(),
                'totalItems' => $subCategories->total(),
                'totalPages' => $subCategories->lastPage(),
            ],
            message: 'Sub Categories data retrieved successfully.'
        );
    }

    /**
     * Get all subcategory options for dropdown selections.
     *
     * @return JsonResponse
     */
    public function options(): JsonResponse
    {
        return $this->successResponse(
            data: SubCategoryResource::collection($this->subCategoryService->options())->resolve(),
            message: 'Sub Category options retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSubCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        $subCategory = $this->subCategoryService->create($request->validated());

        return $this->successResponse(
            data: new SubCategoryResource($subCategory),
            message: 'Sub Category created successfully.',
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        return $this->successResponse(
            data: new SubCategoryResource($subCategory->load('category')),
            message: 'Sub Category data retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSubCategoryRequest $request
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        $subCategory = $this->subCategoryService->update($subCategory, $request->validated());

        return $this->successResponse(
            data: new SubCategoryResource($subCategory),
            message: 'Sub Category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory): JsonResponse
    {
        $this->subCategoryService->delete($subCategory);

        return $this->successResponse(
            message: 'Sub Category deleted successfully.'
        );
    }
}
