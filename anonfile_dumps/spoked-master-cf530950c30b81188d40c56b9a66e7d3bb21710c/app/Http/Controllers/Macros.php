<?php

namespace App\Http\Controllers;

use App\Macro;
use Illuminate\Http\Request;

class Macros extends Controller
{
    public function index(Request $request){
        $records = Macro::all();

        if($request->isMethod('post')){
            Macro::find($request->get('del'))->delete();
            return redirect(route('macros'));
        }

        return view('macros/list', compact(
            'records'
        ));
    }

    public function edit(Request $request, $id = null){
        $record = Macro::findOrNew($id);

        if($request->isMethod('post')){
            $record->name = $request->get('name');

            if($record->exists){
                $this->validate($request, Macro::$rules, [], Macro::attributes());
                $record->value = $request->get('value');
            }else{
                $record->value = file_get_contents(
                    $request->file('file')->getRealPath()
                );
            }

            $record->save();
            return redirect(route('macros'));
        }

        return view('macros/edit', compact(
            'record'
        ));
    }
}