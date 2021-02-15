<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller {

    private $products;

    public function __construct() {
        $this->products = [
            [
                'id' => 1,
                'name' => 'product1'
            ],
            [
                'id' => 2,
                'name' => 'product2'
            ],
            [
                'id' => 3,
                'name' => 'product3'
            ],
        ];
    }

    /**
     * GET
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'data' => $this->products,
        ]);
    }

    /**
     * GET
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'to create the resource use POST method to /product'
        ]);
    }

    /**
     * POST
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'new product created'
        ]);
    }

    /**
     * GET
     * Display the specified resource.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'display product',
            'data' => collect($this->products)->where('id', $id)->first()
        ]);
    }

    /**
     * GET
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(int $id, Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'edit form for product',
            'data' => collect($this->products)->where('id', $id)->first()
        ]);
    }

    /**
     * PUT/PATCH
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'product updated',
            'data' => collect($this->products)->where('id', $id)->first()
        ]);
    }

    /**
     * DELETE
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, Request $request) {
        return new JsonResponse([
            'resource_method' => __METHOD__,
            'verb' => $request->method(),
            'message' => 'product destroyed',
            'data' => collect($this->products)->where('id', $id)->first()
        ]);
    }
}
