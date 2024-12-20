<?php

namespace App\Http\Controllers\Admin;

use App\Album;
use App\Http\Controllers\Controller;
use App\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:album-list|album-create|album-edit|album-delete|album-show', ['only' => ['index', 'show']]);
        $this->middleware('permission:album-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:album-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:album-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $title = 'List Album';
        return view('admin.album.index', compact('title'));
    }

    public function getAlbums(Request $request)
    {
        if ($request->ajax()) {
            $data = Album::with(['photos'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('photos_count', function ($row) {
                    return $row->photos->count();
                })
                ->editColumn('status', function ($row) {
                    return ($row->status == 0) ? "Tidak Aktif" : "Aktif";
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->addColumn('action', function ($row) {
                    $edit_url = route('admin.album.edit', $row->id);
                    $show_url = route('admin.album.show', $row->id);
                    $actionBtn = '
                <a class="btn btn-success album_detail" href="' . $show_url . '">
                <i class="far fa-info-circle"></i>
                </a>
                <a class="btn btn-info album_edit" href="' . $edit_url . '">
                <i class="far fa-edit"></i>
                </a>
                <a class="btn btn-danger hapus_record" data-id="' . $row->id . '" data-name="' . $row->name . '" href="#">
                <i class="far fa-trash"></i>
                </a>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'tags'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Bikin Album';
        return view('admin.album.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
            'detail' => 'required|min:5',
            'status' => 'required|integer',
        ]);

        $slug = Str::of($request->name)->slug('-');
        if ($slug->contains($slug)) {
            $album_slug = Album::where('slug', $slug)->get();
            if ($album_slug->count() > 0) {
                $slug = ($slug . '-' . Str::of($album_slug->count())->slug('-'));
            } else {
                $slug;
            }
        }

        Album::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'slug' => $slug,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.album')->with('success', 'Album berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        $title = 'List Photo Album';
        $photos = Photo::where('album_id', $album->id)->latest()->get();
        return view('admin.album.show', compact('title', 'album', 'photos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Edit Kategori Rapat";
        $album = Album::findOrFail($id);
        return view('admin.album.edit', compact('title', 'album'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $album = Album::find($id);
        $this->validate($request, [
            'name' => 'required|min:5',
            'detail' => 'required|min:5',
            'status' => 'required|integer',
        ]);

        $slug = Str::of($request->name)->slug('-');
        if ($slug->contains($slug)) {
            $album_slug = Album::where('slug', $slug)->get();
            if ($album_slug->count() > 0) {
                $slug = ($slug . '-' . Str::of($album_slug->count())->slug('-'));
            } else {
                $slug;
            }
        }

        $album->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'slug' => $slug,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.album')->with('success', 'Album berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();
        return response()->json(['status' => TRUE]);
    }
}
