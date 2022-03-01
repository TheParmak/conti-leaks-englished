<?php defined('SYSPATH') or die('No direct script access.');

class Model_Silent extends Model {

    protected $errors;
    public function getErrors(){ return $this->errors; }

    public function getAll(){
        $query = "SELECT getsilent()";
        $query = DB::query(Database::SELECT, $query);
        $result = $query->execute();
        $out = array();
        foreach($result as $item){
            $out[] = str_getcsv(trim($item['getsilent'], '()'));
        }
        return $out;
    }

    public function add(){
        $data = Arr::extract($_POST, [
            ':argNet',
            ':argSystem',
            ':argLocation'
        ]);

        $validation = Validation::factory($data)
            ->label(':argNet', 'Net')
            ->rule(':argNet', 'not_empty')
            ->label(':argSystem', 'System')
            ->rule(':argSystem', 'not_empty')
            ->label(':argLocation', 'Location')
            ->rule(':argLocation', 'not_empty');

        if( $validation->check() ){
            $query = "SELECT addsilent(:argNet, :argSystem, :argLocation)";
            $query = DB::query(Database::SELECT, $query);
            $query->parameters($data);
            $query->execute();
        }else{
            $this->errors = $validation->errors("validation");
        }
    }

    public function delete($id){
        $query = "SELECT deletesilent(:argID)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters(array(
            ':argID' => $id
        ));
        $query->execute();
    }
}
