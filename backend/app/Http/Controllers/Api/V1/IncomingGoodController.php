<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncomingGoodRequest;
use App\Http\Requests\UpdateIncomingGoodRequest;
use App\Http\Resources\IncomingGoodResource;
use App\Models\IncomingGood;
use App\Services\IncomingGoodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IncomingGoodController
 *
 * Handles API requests and responses related to incoming goods management.
 *
 * @package App\Http\Controllers\Api\V1
 */
class IncomingGoodController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param IncomingGoodService $incomingGoodService
     */
    public function __construct(private readonly IncomingGoodService $incomingGoodService)
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
        $perPage = (int) $request->integer('limit', 10);
        $filters = $request->only([
            'search',
            'category_id',
            'sub_category_id',
            'operator_id',
            'year',
            'is_verified',
        ]);

        $incomingGoods = $this->incomingGoodService->paginate($perPage, $filters);

        return $this->successResponse(
            data: IncomingGoodResource::collection($incomingGoods->items()),
            meta: [
                'page' => $incomingGoods->currentPage(),
                'limit' => $incomingGoods->perPage(),
                'totalItems' => $incomingGoods->total(),
                'totalPages' => $incomingGoods->lastPage(),
            ],
            message: 'Incoming goods data retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreIncomingGoodRequest $request
     * @return JsonResponse
     */
    public function store(StoreIncomingGoodRequest $request): JsonResponse
    {
        $incomingGood = $this->incomingGoodService->create($request->validated());

        return $this->successResponse(
            data: new IncomingGoodResource($incomingGood->load(['operator', 'subCategory', 'items'])),
            message: 'Incoming good created successfully.',
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param IncomingGood $incomingGood
     * @return JsonResponse
     */
    public function show(IncomingGood $incomingGood): JsonResponse
    {
        return $this->successResponse(
            data: new IncomingGoodResource($incomingGood->load(['operator', 'subCategory.category', 'items', 'verifier'])),
            message: 'Incoming good data retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateIncomingGoodRequest $request
     * @param IncomingGood $incomingGood
     * @return JsonResponse
     */
    public function update(UpdateIncomingGoodRequest $request, IncomingGood $incomingGood): JsonResponse
    {
        $incomingGood = $this->incomingGoodService->update($incomingGood, $request->validated());

        return $this->successResponse(
            data: new IncomingGoodResource($incomingGood->load(['operator', 'subCategory', 'items'])),
            message: 'Incoming good updated successfully.'
        );
    }

    /**
     * Verify the specified incoming good.
     *
     * @param IncomingGood $incomingGood
     * @return JsonResponse
     */
    public function verify(IncomingGood $incomingGood): JsonResponse
    {
        $incomingGood = $this->incomingGoodService->verify($incomingGood);

        return $this->successResponse(
            data: new IncomingGoodResource($incomingGood->load(['operator', 'subCategory', 'items', 'verifier'])),
            message: 'Incoming good verified successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param IncomingGood $incomingGood
     * @return JsonResponse
     */
    public function destroy(IncomingGood $incomingGood): JsonResponse
    {
        $this->incomingGoodService->delete($incomingGood);

        return $this->successResponse(
            message: 'Incoming good deleted successfully.'
        );
    }
}
