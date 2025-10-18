<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Category as ModelsCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $validated = $request->validated();

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, category $category)
    {
        $validated = $request->validated();
        $category->update($validated);

        return response()->json(
            [
                'message' => 'Category updated',
                'data' => $category
            ]
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted',
            'id' => $category->id
        ]);
    }
}
