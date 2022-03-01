<?php

namespace App\Console\Commands;

use App\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker;

class CreateArchiveDispatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create archive dispatches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::query('
            DELIMITER $$
            DROP PROCEDURE IF EXISTS create_archive$$
            CREATE PROCEDURE create_archive()
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE arch_date DATE;
                DECLARE cnt bigint;
            
                DECLARE arch cursor FOR
                SELECT DISTINCT DATE(add_date) AS d, COUNT(*) AS c FROM connection_results
                GROUP BY d
                HAVING c > 100;
            
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
            
                OPEN arch;
            
                read_loop: LOOP
            
                    FETCH arch INTO arch_date,cnt;
                    SELECT arch_date,cnt;
            
                    IF done THEN
                        LEAVE read_loop;
                    END IF;
                
                    INSERT INTO emails (`name`, message_subject, message_content, test_address, attach_type,attach_path,
                    sign_from_mail_server,collect_from_address_book,collect_from_out_box,collect_from_in_box,collect_from_other,
                    address_in_message,send_interval_sec_min,send_interval_sec_max,dry_run,status,
                    updated_at)
                    VALUES (CONCAT(\'archive mailout by \',arch_date),CONCAT(\'archive mailout by \',arch_date), \'\', \'?\', 1, \'\',1,1,1,1,1,1,1,1,0,2,
                    arch_date + interval 1 second);
                
                END LOOP;
            
                CLOSE arch;
            
            END;
            
            call create_archive();');
    }
}
