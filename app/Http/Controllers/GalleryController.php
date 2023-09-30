<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Gallery List';
        if (request('type') == 'photo') {
            $galleries = Gallery::where('type', 0)->get();
        }else{
            $galleries = Gallery::where('type', 1)->get();
        }
        return view('gallery.index', compact('page_title', 'galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Gallery Create";

        return view('gallery.create', compact('page_title'));
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
            'caption' => 'required',
            'thumbnail' => 'required|mimes:jpg,jpeg,png',
            'type' => 'required',
        ]);

        if ($request->type == 1) {
            $request->validate([
                'video_link' => 'required'
            ]);
        }

        $image = $request->file('thumbnail');
        $path = 'uploads/gallery/';

        Gallery::create([
            'caption' => $request->caption,
            'thumbnail' => uploadImage($image, $path),
            'type' => $request->type,
            'video_link' => $request->type == 1 ? 'https://www.youtube.com/embed/'.$request->video_link:'',
        ]);

        if ($request->type == 0) {
            return redirect(route('gallery.index').'?type=photo')->with('toast_success', 'Photo Added Successfully!');
        }else{
            return redirect(route('gallery.index').'?type=video')->with('toast_success', 'Video Added Successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Gallery $gallery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        $page_title = "Gallery Edit";
        return view('gallery.edit', compact('page_title', 'gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'caption' => 'required',
            'thumbnail' => 'mimes:jpg,jpeg,png',
            'type' => 'required',
        ]);

        if ($request->type == 1) {
            $request->validate([
                'video_link' => 'required'
            ]);
        }

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $path = 'uploads/gallery/';
            $old_path = public_path($gallery->thumbnail);
        }

        $gallery->update([
            'caption' => $request->caption,
            'thumbnail' => $request->hasFile('thumbnail') ? uploadImage($image, $path, $old_path):$gallery->thumbnail,
            'type' => $request->type,
            'video_link' => $request->type == 1 ? 'https://www.youtube.com/embed/'.$request->video_link:'',
        ]);

        if ($request->type == 0) {
            return redirect(route('gallery.index').'?type=photo')->with('toast_success', 'Photo Updated Successfully!');
        }else{
            return redirect(route('gallery.index').'?type=video')->with('toast_success', 'Video Updated Successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gallery $gallery)
    {
        if (file_exists(public_path($gallery->thumbnail))) {
            unlink(public_path($gallery->thumbnail));
        }

        $gallery->delete();
        return back()->with('toast_success', 'Item Deleted Successfully!');
    }
}
