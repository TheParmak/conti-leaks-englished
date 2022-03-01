<?php

namespace App\Http\Controllers\Angular;

use App\Macro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class Macros extends Controller
{
    public function get($id = null){
        if($id){
            return response()->json(Macro::find($id));
        }else{
            return response()->json(Macro::all());
        }
    }

    public function add(Request $request){
        // TODO if use Macro::create or new Macro() mutator with pg_escape_bytea used double
        $macros = Macro::findOrNew(null);
        $macros->fill([
            'name' => $request->get('name'),
            'value' => implode(PHP_EOL, array_map('trim', explode(',', $request->get('value')))).PHP_EOL
        ]);
        $macros->save();
        return $macros->id;
    }

    public function del($id){
        Macro::destroy($id);
    }

    public function global_get(){
        $file_name = 'global_macros.txt';
        $storage = Storage::disk('config');
        $config = $storage->get($file_name);

        return response($config);
    }

    public function global_upd(Request $request){
        $file_name = 'global_macros.txt';
        $storage = Storage::disk('config');

        $storage->put(
            $file_name, $request->get('global')
        );
    }
}
