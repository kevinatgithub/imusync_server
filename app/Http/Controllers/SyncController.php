<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DonorLeft;
use App\DonorRight;
use App\DonorFinal;

class SyncController extends Controller
{
    function pull($id){
        $q = DB::select("select count(*) as cnt from donor1");
        $count = $q[0]->cnt;
        
        $length = round($count/7);
        // $length = 100;
        $start = $id * $length;

        $donors = DonorLeft::with('right')->orderBy('seqno','asc')->skip($start)->take($length)->get()->toArray();

        $data = [];
        foreach($donors as $i => $d){
            
            $right = $d['right'];

            // if($d['lname'] == "SALIMBOT"){
            //     dd($d);
            // }
            
            if($d['donor_photo'] != null){
                $d['donor_photo'] = $this->photo($d['seqno']);
            }

            if($right['donor_photo'] != null){
                $right['donor_photo'] = $this->photo($d['seqno']);
            }
            
            $d['id'] = $d['seqno']."l";
            $right['id'] = $right['seqno']."r";

            $data[$i] = [
                'left' => $d,
                'right' => $right,
            ];
            unset($data[$i]['left']['right']);
        }

        // dd($x);

        // $data = $this->utf8ize($data);
        return ['data' => $data];
    }

    function push($id,Request $request){
        $data = $request->get('changes');
        foreach($data as $i => $r){
            $d = new DonorFinal();
            $d->donor_photo = isset($r['donor_photo']) ? $r['donor_photo'] : null;
            $d->seqno = isset($r['seqno']) ? $r['seqno'] : null;
            $d->donor_id = isset($r['donor_id']) ? $r['donor_id'] : null;
            $d->name_suffix = isset($r['name_suffix']) ? $r['name_suffix'] : null;
            $d->lname = isset($r['lname']) ? $r['lname'] : null;
            $d->fname = isset($r['fname']) ? $r['fname'] : null;
            $d->mname = isset($r['mname']) ? $r['mname'] : null;
            $d->bdate = isset($r['bdate']) ? $r['bdate'] : null;
            $d->gender = isset($r['gender']) ? $r['gender'] : null;
            $d->civil_stat = isset($r['civil_stat']) ? $r['civil_stat'] : null;
            $d->tel_no = isset($r['tel_no']) ? $r['tel_no'] : null;
            $d->mobile_no = isset($r['mobile_no']) ? $r['mobile_no'] : null;
            $d->email = isset($r['email']) ? $r['email'] : null;
            $d->nationality = isset($r['nationality']) ? $r['nationality'] : null;
            $d->occupation = isset($r['occupation']) ? $r['occupation'] : null;
            $d->home_no_st_blk = isset($r['home_no_st_blk']) ? $r['home_no_st_blk'] : null;
            $d->home_brgy = isset($r['home_brgy']) ? $r['home_brgy'] : null;
            $d->home_citymun = isset($r['home_citymun']) ? $r['home_citymun'] : null;
            $d->home_prov = isset($r['home_prov']) ? $r['home_prov'] : null;
            $d->home_region = isset($r['home_region']) ? $r['home_region'] : null;
            $d->home_zip = isset($r['home_zip']) ? $r['home_zip'] : null;
            $d->office_no_st_blk = isset($r['office_no_st_blk']) ? $r['office_no_st_blk'] : null;
            $d->office_brgy = isset($r['office_brgy']) ? $r['office_brgy'] : null;
            $d->office_citymun = isset($r['office_citymun']) ? $r['office_citymun'] : null;
            $d->office_prov = isset($r['office_prov']) ? $r['office_prov'] : null;
            $d->office_region = isset($r['office_region']) ? $r['office_region'] : null;
            $d->office_zip = isset($r['office_zip']) ? $r['office_zip'] : null;
            $d->donation_stat = isset($r['donation_stat']) ? $r['donation_stat'] : null;
            $d->donor_stat = isset($r['donor_stat']) ? $r['donor_stat'] : null;
            $d->deferral_basis = isset($r['deferral_basis']) ? $r['deferral_basis'] : null;
            $d->facility_cd = isset($r['facility_cd']) ? $r['facility_cd'] : null;
            $d->lfinger = isset($r['lfinger']) ? $r['lfinger'] : null;
            $d->rfinger = isset($r['rfinger']) ? $r['rfinger'] : null;
            $d->created_by = isset($r['created_by']) ? $r['created_by'] : null;
            $d->created_dt = isset($r['created_dt']) ? $r['created_dt'] : null;
            $d->updated_by = isset($r['updated_by']) ? $r['updated_by'] : null;
            $d->updated_dt = isset($r['updated_dt']) ? $r['updated_dt'] : null;
            $d->save();
            unset($r);
        }

        return ['status' => 'ok','size' => count($data)];
    }

    function test(){
        $d = new DonorFinal;
        $d->seqno = "asd";
        $d->bdate = date("Y-m-d");
        $d->save();
    }

    function utf8ize( $mixed ) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }
        return $mixed;
    }

    function photo($seqno){
        $donor = DonorLeft::select('seqno','donor_photo')->whereSeqno($seqno)->first();
        if(!$donor){
            return ['status' => 'ok','data'=>null];
        }

        // $photo = base64_encode($donor->donor_photo);
        // return ['status' => 'ok', 'data' => $photo];
        // dd($this->compressPhoto($donor->donor_photo));
        // $photo = base64_encode($this->compressPhoto($donor->donor_photo));
        $photo = $this->compressPhoto($donor->donor_photo);
        $photo = base64_encode($photo);
        // exit($photo);
        // return ['status' => 'ok', 'data' => $photo];
        return $photo;
    }

    private function compressPhoto($base64){
        if ($base64) {
            $percent = 0.3;
        
            // $data = base64_decode($base64);
            $data = $base64;
            try{
                $im = @imagecreatefromstring($data);
            }catch(Exception $e){
                return null;
            }
            $width = imagesx($im);
            $height = imagesy($im);
            $newwidth = $width * $percent;
            $newheight = $height * $percent;
        
            $thumb = imagecreatetruecolor($newwidth, $newheight);
        
            // Resize
            imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
       
            ob_start(); // Let's start output buffering.
                imagejpeg($thumb); //This will normally output the image, but because of ob_start(), it won't.
                $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
            // dd($thumb);
            // imagejpeg($thumb);
            return $contents;
            // Output
        }
        return null;
    }
}
