<?php

namespace App\Http\Controllers;

use App\Macro;
use App\Email;
use App\File;
use App\FileList;
use App\PrintRLineFormatter;
use App\RandomByte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

class Emails extends Controller
{
    public function index(Request $request){
        $emails = Email::all();

        $active = $emails->where('status', '=', '1');
        $sent = $emails->where('status', '=', '2')->sortByDesc('updated_at');

        $drafts = $emails->where('status', '=', '3');

        unset($emails);

        //      DELETE EMAIL ACTION HANDLER
        if($request->isMethod('post') && $request->get("action") == "delete")
            return $this->delete_email($request->get('id'));



        //      SET ACTIVE EMAIL ACTION HANDLER
        if($request->isMethod('post') && $request->get("action") == "active")
            return $this->active_email($request->get('id'));



        //      SET CLONE EMAIL ACTION HANDLER
        if($request->isMethod('post') && $request->get("action") == "clone")
            return $this->clone_email($request->get('id'));


        //      DEFAULT
        return view('emails.list', compact(
            'active',
            'sent',
            'drafts'
        ));
    }

    public function edit(Request $request, $id = null){
        $selected = [];
        $macros = json_encode(Macro::all());
        $macros_global = Storage::disk('config')->get('global_macros.txt');
        $email = Email::findOrNew($id);
        $emails_macros = $email->macros()->get()->map(function($value){
            return $value->id;
        })->toArray();

        $files = FileList::all()->pluck('name', 'id')->toArray();
        $files[0] = '--';
        $list_files = array_filter(FileList::all()->mapWithKeys(function($value){
            return [$value->name => $value->files()->pluck('name', 'id')];
        })->toArray());
        $list_files[0] = '--';

        /*if($email->attach_type == 'one_file_from_dir'){
            $selected['one_file_from_dir'] = $email->attach_path;
        }elseif ($email->attach_type == 'file_path'){
            $selected['file_path'] = $email->attach_path;
        }else{
            $selected['download_url'] = $email->attach_path;
        }*/
        $selected[$email->attach_type] = $email->attach_path;

        // TODO need write by zen
        if($request->isMethod('post')){
            $post = $request->all();

            $monolog = new Logger('EmailsSave');
            $monolog->pushHandler(
                (new StreamHandler(storage_path('logs/emails_save.log'), Logger::INFO))
                    ->setFormatter(new LineFormatter(null, null, true))
            );
            $monolog->addInfo(print_r(array_except($post, ['_token', 'action']), true));
            $post['attach_path'] = $post['attach_path'][$post['attach_type']];
            if($post['attach_type'] == 'download_url' && $post['name']['download_url'] == null){
                $post['name'] = pathinfo($post['attach_path'])['filename'];
            }else{
                $post['name'] = $post['name'][$post['attach_type']];
            }
            if(isset($post['source_files_names_macro'][$post['attach_type']])){
                $post['source_files_names_macro'] = $post['source_files_names_macro'][$post['attach_type']];
            }else{
                $post['source_files_names_macro'] = null;
            }
            if(isset($post['is_compress_to_zip'][$post['attach_type']])){
                $post['is_compress_to_zip'] = boolval($post['is_compress_to_zip'][$post['attach_type']]);
            }else{
                $post['is_compress_to_zip'] = null;
            }
            if($post['attach_type'] == "none") {
                $post["name"] = "";
                $post["attach_path"] = "none";
            }

            $request->merge($post);
            $this->validate($request, Email::$rules, [], Email::attributes());
            $email->fill($post);

            date_default_timezone_set('UTC');

            // CREATE NEW EMAIL
            if (!isset($email->status))
            {
                $email->status = 3;

                $email->created_at = date('Y-m-d H:i:s');
            }

            $email->updated_at = date('Y-m-d H:i:s');
            $email->mailout_type = intval($request->get("mailout_type"));

            $email->save();

            $email->random_bytes()->delete();
            if($request->exists('random_patch_bytes_dec')){
                foreach($request->get('random_patch_bytes_dec') as $item){
                    $email->random_bytes()->create([
                        'value' => $item
                    ]);
                }
            }

            $email->random_names()->delete();
            if($request->exists('random_name')){
                foreach($request->get('random_name')[$post['attach_type']] as $item){
                    $email->random_names()->create([
                        'value' => $item
                    ]);
                }
            }

            $email->macros()->sync($request->get('macros'));

            $tab = 'draft';

            if ($request->get("action") == "active")
            {
                $this->active_email($email->id);
                $tab = 'active';
            }

            if ($request->get("action") == "clone")
            {
                $this->clone_email($email->id);
                $tab = 'sent';
            }

            return redirect(route('emails_index', ['tab' => $tab]));
        }

        $labels = Email::attributes();
        $types = [0 => "Outlook", 1 => "Webmail (via Gmail, Yahoo and etc...)", 2 => "All Targets (both Outlook and Web Mail)"];

        return view('emails.edit', compact(
            'email',
            'emails_macros',
            'list_files',
            'files',
            'selected',
            'macros',
            'macros_global',
            'labels',
            'types'
        ));
    }


    private function delete_email ($id) {
        Email::destroy($id);
        return redirect(route('emails_index'));
    }

    private function active_email($id) {
        Email::where(["status" => 1])->update(["status" => 2]);
        Email::where(["id" => $id])->update(["status" => 1]);

        Email::genConf($id);

        return redirect(route('emails_index'));
    }

    private function clone_email($id) {
        $cEmail = Email::where(["id" => $id])->first();

        $newMail = $cEmail->replicate();
        date_default_timezone_set('UTC');
        $newMail->status = 3;
        $newMail->updated_at = date('Y-m-d H:i:s');
        $newMail->created_at = date('Y-m-d H:i:s');
        $newMail->push();
        $cEmail->relations = [];
        $cEmail->load('random_names', 'random_bytes', 'emails_macros');

        $relations = $cEmail->getRelations();

        foreach ($relations as $relation)
        {
            foreach ($relation as $relationRecord)
            {
                $newRelationship = $relationRecord->replicate();
                $newRelationship->email_id = $newMail->id;
                $newRelationship->push();
            }
        }

        $newMail->save();

        return redirect(route('emails_index'));
    }
}