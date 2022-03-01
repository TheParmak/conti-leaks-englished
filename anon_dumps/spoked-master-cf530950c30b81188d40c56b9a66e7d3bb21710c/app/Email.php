<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Email extends Model{

    public $timestamps = false;
    protected $fillable = [
        'message_subject',
        'test_address',
        'message_content',
        'attach_type',
        'attach_path',
        'name',
        'is_compress_to_zip',
        'source_files_names_macro',
        'max_patch_send',
        'sign_from_mail_server',
        'collect_from_address_book',
        'collect_from_out_box',
        'collect_from_in_box',
        'collect_from_other',
        'address_in_message',
        'send_interval_sec_min',
        'send_interval_sec_max',
        'dry_run',
        'randomize_attach',
        'randomize_string'
    ];

    public function file(){
        return $this->hasOne(FileList::class);
    }

    public function random_bytes(){
        return $this->hasMany(RandomByte::class);
    }

    public function random_names(){
        return $this->hasMany(RandomName::class);
    }

    public function macros(){
        return $this->belongsToMany(Macro::class, 'emails_macros');
    }

    public function emails_macros(){
        return $this->hasMany(EmailsMacros::class);
    }

    public static $rules = [
        'message_subject' => 'required',
        'test_address' => 'required|email',
        'message_content' => 'required',
        'attach_path' => 'required|not_in:0',
        'attach_type' => 'required',
        'name' => 'required_if:attach_type,one_file_from_dir|required_if:attach_type,file_path',
        'random_patch_bytes_dec' => 'array',
        'random_patch_bytes_dec.*' => 'integer',
        'max_patch_send' => 'integer',
        'address_in_message' => 'required',
        'send_interval_sec_min' => 'required',
        'send_interval_sec_max' => 'required',
    ];

    public static function attributes(){
        return [
            'message_subject' => 'Message subject',
            'test_address' => 'Control Email Address',
            'message_content' => 'Message content',
            'attach_type' => 'Attach type',
            'attach_path' => 'Attach path',
            'name' => 'Backup Name',
            'random_name' => 'Random names',
            'is_compress_to_zip' => 'Compress to zip',
            'source_files_names_macro' => 'Source files names macro',
            'random_patch_bytes_dec' => 'Path these bytes',
            'max_patch_send' => 'Randomize bytes after every N messages has been sent',
            'sign_from_mail_server' => 'Add Signature from user email client',
            'collect_from_address_book' => 'Collect from Address Book',
            'collect_from_out_box' => 'Collect from Outbox',
            'collect_from_in_box' => 'Collect from Inbox',
            'collect_from_other' => 'Collect from other folders',
            'address_in_message' => 'Address in message',
            'send_interval_sec_min' => 'Minimum send interval between two messages (seconds)',
            'send_interval_sec_max' => 'Maximum send interval between two messages (seconds)',
            'dry_run' => 'DRY RUN (DO NOT SEND EMAIL, COLLECT ADDRESSES ONLY)',
            'make_active' => 'Make this mailout active',
            'mailout_type' => 'Type of mailout',
            'mailout_is_inactive' => 'This mailout is INACTIVE. Click to activate it.',
            'mailout_clone' => 'Clone this mailout'
        ];
    }

    public static function genConf($id){
        $email = Email::find($id);

        $storage_config = Storage::disk('config');
        $config = collect(json_decode($storage_config->get('database.json')))->map(function ($value){
            return ['@value' => $value];
        })->toArray();

        $config_general = json_decode($storage_config->get('general.json'));

        $blackList = PHP_EOL.implode(PHP_EOL, collect(json_decode($storage_config->get('blacklist.json')))->map(function ($value){
                return "\t\t".'<NoSendIfNameContain value="'.$value.'"/>';
            })->toArray()).PHP_EOL;

        $storage_emails = Storage::disk('emails');
        $storage_emails->deleteDirectory($email->id);
        $storage_emails->put($email->id.'/MessageSubject.txt', $email->message_subject);
        $storage_emails->put($email->id.'/MessageContent.html', $email->message_content);

        $macros = $email->macros()->get();
        foreach($macros as $dict){
            if(env('DB_CONNECTION') == 'mysql') {
                $storage_emails->put($email->id.'/dict/'.$dict->name.'.txt', pg_unescape_bytea($dict->value));
            }else{
                $storage_emails->put($email->id.'/dict/'.$dict->name.'.txt', $dict->value);
            }
        }

        $macros = PHP_EOL.implode(PHP_EOL, $macros->map(function($dict) use($email){
                $file_path = storage_path('emails/'.$email->id.'/dict/'.$dict->name.'.txt');
                return "\t".'<ClientTextMacrosDictionary name="'.$dict->name.'" file_path="'.$file_path.'"/>';
            })->toArray()).PHP_EOL;

        $array = [
            'MySQLConfig' => $config,
            'TestAddress' => ['@value' => $email->test_address],
            'MessageSubject' => ['@file_path' => storage_path('emails/'.$email->id.'/MessageSubject.txt')],
            'MessageContent' => ['@file_path' => storage_path('emails/'.$email->id.'/MessageContent.html')],
            '*' => $macros,
            'ClientGlobalTextMacros' => ['@value' => $storage_config->get('global_macros.txt')],
            'MailClientNoSendAddress' => [
                '*' => $blackList,
                'NoSendIfDomainContain' => ['@value' => '@sample'],
            ],
            'MailClientCollectEmailsParams' => [
                '@collect_from_address_book' => self::boolToString($email->collect_from_address_book),
                '@collect_from_out_box' => self::boolToString($email->collect_from_out_box),
                '@collect_from_in_box' => self::boolToString($email->collect_from_in_box),
                '@collect_from_other' => self::boolToString($email->collect_from_other),
            ],
            'MailClientSendParams' => [
                '@address_in_message' => $email->address_in_message,
                '@send_interval_sec_min' => $email->send_interval_sec_min,
                '@send_interval_sec_max' => $email->send_interval_sec_max,
                '@dry_run' => $email->dry_run ? 1 : 0,
                'SendMessagesLocationsOrder' => [
                    'EmailsFromAddressBook' => '',
                    'EmailsFromOutBox' => '',
                    'EmailsFromInBox' => '',
                    'EmailsFromOther' => '',
                ]
            ],
            'ServerPort' => $config_general->ServerPort,
            'UseSSL' => $config_general->UseSSL,
        ];

        if($email->attach_type == 'one_file_from_dir'){
            $files = FileList::find($email->attach_path)->files()->get();
            foreach ($files as $file){
                if(env('DB_CONNECTION') == 'mysql'){
                    // todo
                    $storage_emails->put($email->id.'/attach/'.$file->name, $file->data);
                }else{
                    $storage_emails->put($email->id.'/attach/'.$file->name, pg_unescape_bytea(stream_get_contents($file->data)));
                }
            }

            $array['MessageAttach'] = [
                '@one_file_from_dir' => storage_path('emails/'.$email->id.'/attach/'),
                '@name' => $email->name,
                '@is_compress_to_zip' => self::boolToString($email->is_compress_to_zip),
            ];

            $array = self::messageAttach($array, $email, $storage_emails);
        }elseif ($email->attach_type == 'file_path'){
            $file = File::find($email->attach_path);
            if(env('DB_CONNECTION') == 'mysql') {
                $storage_emails->put($email->id.'/attach/'.$file->name, $file->data);
            }else{
                $storage_emails->put($email->id . '/attach/' . $file->name, pg_unescape_bytea(stream_get_contents($file->data)));
            }

            $array['MessageAttach'] = [
                '@file_path' => storage_path('emails/'.$email->id.'/attach/'.$file->name),
                '@name' => $email->name,
                '@is_compress_to_zip' => self::boolToString($email->is_compress_to_zip),
            ];

            $array = self::messageAttach($array, $email, $storage_emails);
        }elseif ($email->attach_type == 'download_url'){ // download
            $array['MessageAttach'] = [
                '@download_url' => $email->attach_path,
                '@name' => $email->name,
            ];
        }
        if ($email->randomize_attach == true) {
            $array['MessageAttach']['@randomize_attach'] = "true";
            $array['MessageAttach']['@randomize_string'] = $email->randomize_string;
            $array['MessageAttach']['@max_patch_send'] = $email->max_patch_send;
        }
        else if(!empty($email->random_bytes()->get()->toArray())){
            $array['MessageAttach']['@random_patch_bytes_dec'] = implode(', ', $email->random_bytes->pluck('value')->toArray());
            $array['MessageAttach']['@max_patch_send'] = $email->max_patch_send;
        }

        $xml = new Array2Xml();
        $xml = $xml->buildXML($array, 'MailServerConfig');
        Storage::put('config.xml', $xml);
    }

    private static function boolToString($bool){
        return $bool ? 'true' : 'false';
    }

    private static function messageAttach($array, $email, $storage_emails){
        if($email->source_files_names_macro){
            $array['MessageAttach']['@source_files_names_macro'] = $email->source_files_names_macro;
        }elseif(!empty($email->random_names->pluck('value')->toArray())){
            $storage_emails->put($email->id.'/attach_names.txt', implode(PHP_EOL, $email->random_names->pluck('value')->toArray()));
            $array['MessageAttach']['@random_name_from_file'] = storage_path('emails/'.$email->id.'/attach_names.txt');
        }

        return $array;
    }
}
