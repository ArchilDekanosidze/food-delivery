<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Category List';
        if (request('type') == 'blog') {
            $categories = Category::where('type', 1)->get();
        }else{
            $categories = Category::where('type', 0)->get();
        }
        return view('category.index', compact('page_title', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Category Create';

        return view('category.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $path = '/uploads/category/';
        }

        Category::create([
            'name' => $request->name,
            'thumbnail' => $request->hasFile('thumbnail') ? uploadImage($image, $path):'',
            'type' => $request->type
        ]);

        if ($request->type == 0) {
            return redirect(route('category.index').'?type=menu')->with('toast_success', 'Category Updated Successfully!');
        }else{
            return redirect(route('category.index').'?type=blog')->with('toast_success', 'Category Updated Successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $page_title = 'Category Edit';
        
        return view('category.edit', compact('page_title', 'category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $path = '/uploads/category/';
            $old_path = public_path($category->thumbnail);
        }

        $category->update([
            'name' => $request->name,
            'thumbnail' => $request->hasFile('thumbnail') ? uploadImage($image, $path, $old_path):$category->thumbnail,
            'type' => $request->type
        ]);

        if ($category->type == 0) {
            return redirect(route('category.index').'?type=menu')->with('toast_success', 'Category Updated Successfully!');
        }else{
            return redirect(route('category.index').'?type=blog')->with('toast_success', 'Category Updated Successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (file_exists(public_path($category->thumbnail))) {
            unlink(public_path($category->thumbnail));
        }
        $category->delete();
        return back()->with('toast_success', 'Category Deleted Successfully');
    }
}
