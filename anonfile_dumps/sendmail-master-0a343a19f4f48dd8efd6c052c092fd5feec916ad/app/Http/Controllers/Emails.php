<?php

namespace App\Http\Controllers;

use App\Email;
use Illuminate\Http\Request;

class Emails extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $emails = Email::orderBy('id', 'DESC')->paginate();

        return view('emails.list', compact(
            'emails'
        ));
    }

    public function edit(Request $request, $id = null)
    {
        $data = $request->all();
        $email = Email::findOrNew($id);

        if($request->method() == 'POST' && !empty($data)){
            $email->fill($request->all());
            /*$email->body = $request->body;
            $email->simple_body = $request->simple_body;*/
            $email->save();
            return redirect('/emails')->with('message', 'Success create!');
        }

        return view('emails.edit', compact(
            'email'
        ));
    }

    public function delete($id){
        Email::destroy($id);
        return redirect('/emails')->with('message', 'Success delete!');
    }
}