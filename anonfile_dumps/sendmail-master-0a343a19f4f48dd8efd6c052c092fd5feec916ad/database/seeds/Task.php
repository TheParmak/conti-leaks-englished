<?php

use App\Email;
use Illuminate\Database\Seeder;

class Task extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $faker = Faker\Factory::create();

        foreach (range(0, 10) as $i){
            self::genTask($faker);
        }
    }

    private function genTask($faker){
        $list = Storage::disk('emails')->allFiles();
        $gitignore = array_search('.gitignore', $list);
        if($gitignore !== false){
            unset($list[$gitignore]);
        }

        $emails_list = [];
        foreach ($list as $k => $v){
            $emails_list['emails/'.$v] = base64_decode($v);
            $resolving = Storage::disk('resolving');
            if($resolving->exists($v) && $resolving->exists($v.'/result')){
                $emails_list['resolving/'.$v] = base64_decode($v).' (resolved)';
            }
        }

        $task = \App\Task::create([
            'name' => $faker->word,
            'email_list' => array_rand($emails_list),
            'email_id' => Email::inRandomOrder()->first()->id,
        ]);

        Storage::disk('data')->makeDirectory($task->id);
        $new_file = '';
        $files = Storage::disk('local')->files($task->id);

        $email = Email::find($task->email_id);

        $new_file .= $email->from;
        $new_file .= "\r\n";
        $new_file .= $email->title;
        $new_file .= "\r\n";
        $new_file .= $email->body;
        $new_file .= "\r\n.\r\n.\r\n";

        foreach ($files as $file){
            $new_file .= basename($file);
            $new_file .= "\r\n";
            $new_file .= base64_encode(Storage::disk('local')->get($file));
            $new_file .= "\r\n";
        }

        /* clear eol last string */
        if(empty($files)){
            $new_file = substr($new_file, 0, -8);
        }else{
            $new_file = substr($new_file, 0, -2);
        }

        Storage::disk('data')->put($task->id.'/body', $new_file);

        if(file_exists(storage_path('data/'.$task->id.'/email_list'))){
            unlink(storage_path('data/'.$task->id.'/email_list'));
        }
        if(preg_match('#^resolving.*$#', $task->email_list)){
            symlink(
                storage_path($task->email_list.'/result'),
                storage_path('data/'.$task->id.'/email_list')
            );
        }else{
            symlink(
                storage_path($task->email_list),
                storage_path('data/'.$task->id.'/email_list')
            );
        }

        Storage::disk('data')->put($task->id.'/smtp_list', "/1\r\n/2\r\n/3\r\n/4\r\n/5\r\n/6\r\n/7\r\n/8");
    }
}
