<?php

namespace App\Http\Controllers;

use App\File;
use App\FileList;
use Illuminate\Http\Request;

class Files extends Controller
{
    public function list_index(Request $request){
        $files = FileList::with('files')->get();

        if(!empty($request->all()) && $request->method('post')){
            $list = FileList::find($request->get('del'));
            $list->files()->delete();
            $list->delete();
            return redirect(route('files_list_index'));
        }

        return view('files.list', compact(
            'files'
        ));
    }

    public function list_edit(Request $request, $id = null){
        $list = FileList::findOrNew($id);

        if(!empty($request->all()) && $request->method('post')){
            $this->validate($request, FileList::$rules, [], FileList::attributes());
            $list->fill($request->all());
            $list->save();
            return redirect(route('files_list', ['id' => $list->id]));
        }

        return view('files.list_edit', compact(
            'list'
        ));
    }

    public function files_index(Request $request, $id){
        $list = FileList::find($id);

        if(!empty($request->all()) && $request->method('post')){
            $file = File::find($request->get('del'));
            $file->delete();
            return redirect(route('files_list', ['id' => $id]));
        }

        return view('files.files', compact(
            'list'
        ));
    }

    public function files_add(Request $request, $id){
        if(!empty($request->file()) && !empty($request->all()) && $request->method('post')){
            ini_set('post_max_size', '8M');
            ini_set('upload_max_filesize', '8M');

            $this->validate($request, File::$rules);
            
            foreach ($request->file('file') as $f){
                $file = new File;
                if(env('DB_CONNECTION') == 'mysql') {
                    $file->data = file_get_contents(
                        $f->getRealPath()
                    );
                }else{
                    $file->data = pg_escape_bytea(
                        file_get_contents(
                            $f->getRealPath()
                        )
                    );
                }
                $file->file_list_id = $id;
                $file->name = $f->getClientOriginalName();
                $file->save();
            }
            return redirect(route('files_list', $id));
        }

        $list = FileList::find($id);
        return view('files.files_edit', compact(
            'list'
        ));
    }
}
