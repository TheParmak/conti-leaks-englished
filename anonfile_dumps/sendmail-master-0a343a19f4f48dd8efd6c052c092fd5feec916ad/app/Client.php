<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Client
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $base64
 * @method static \Illuminate\Database\Query\Builder|\App\Client whereBase64($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Client whereId($value)
 * @property-read \App\Blacklist $blacklist
 */
class Client extends Model{

    public $timestamps = false;
    protected $fillable = ['base64'];
    protected $guarded = ['id'];

    public function blacklist(){
        return $this->hasOne(Blacklist::class, 'base64', 'base64');
    }

    /* */

    private static function getBase($base64){
        $client = base64_decode($base64);
        preg_match('#([\.\d\w\:]+)\\t(\d+)\\t(\d+)#', $client, $match);
        return $match;
    }

    public static function isValid($base64){
        $client = self::getBase($base64);
        if( isset($client[1]) && isset($client[2]) && isset($client[3]) ){
            return true;
        }else{
            return false;
        }
    }

    public static function getOsVer($base64){
        $client = self::getBase($base64);
        $key = $client[2].'.'.$client[3];
        if(isset(config('system')[$key])){
            return config('system')[$key];
        }else{
            return '--';
        }
    }

    public static function getBase64ByIP($ip){
        $ip = base64_encode($ip);
        $files = Storage::disk('client_data')->allFiles();
        $files = preg_replace('#\/#', '', $files);
        return preg_grep('#'.$ip.'#', $files)[0];
    }

    public static function getBase64Cut($base64){
        $arr = explode("\t", base64_decode($base64));
        if(count($arr) == 4){
            array_pop($arr);
            $base64 = base64_encode(implode("\t", $arr));
        }
        return $base64;
    }

    public static function getIP($base64){
        return self::getBase($base64)[1];
    }

    public static function getDomain($base64){
        $path = self::getPathFile($base64);
        if(Storage::disk('client_data')->exists($path)){
            $domain = file(storage_path('client_data/'.$path))[0];
            return preg_replace("#\r\n#", '', $domain);
        }else{
            return false;
        }
    }

    public static function getPathFile($b){
        return $b[0].$b[1].'/'.$b[2].$b[3].'/'.$b[4].$b[5].'/'.substr($b, 6);
    }

    public static function getCountry($base64){
        return geoip_record_by_name(self::getIP($base64))['country_code'] ?: '--';
    }

    public static function getBlack($base64){
        $blacklist = Blacklist::where('base64', $base64)
            ->first();

        if(!empty($blacklist)){
            return $blacklist->valid;
        }else{
            return null;
        }
    }

    public static function getOnline(){
        $client = new \GuzzleHttp\Client();
        if(App::environment() != 'local'){
            $response = $client->get(config('api.get_online'), ['http_errors' => false]);
            if($response->getStatusCode() == 200){
                $data = json_decode($response->getBody()->getContents(), true);
                $clients = array_keys($data);
                foreach ($clients as $c){
                    if(!self::validIP($c)){
                        unset($data[$c]);
                    }
                }
                return $data;
            }
        }else{
            $data = json_decode(Storage::get('online.json'), true);
            $clients = array_keys($data);
            foreach ($clients as $c){
                if(!self::validIP($c)){
                   unset($data[$c]);
                }
            }
            return $data;
        }
    }

    public static function validIP($base64){
        if(isset(self::getBase($base64)[1])){
            return filter_var(self::getIP($base64), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }else{
            return false;
        }
    }
}
