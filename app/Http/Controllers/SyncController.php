<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DonorLeft;
use App\DonorRight;

class SyncController extends Controller
{
    function pull($id){
        $q = DB::select("select count(*) as cnt from donor1");
        $count = $q[0]->cnt;
        
        $length = round($count/7);
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

    function push(Request $request){
        $id = $request->get('id');
        $data = $request->get('changes');
        foreach($data as $i => $r){
            $d = new DonorFinal();
            $d->donor_photo = $r->donor_photo;
            $d->seqno = $r->seqno;
            $d->donor_id = $r->donor_id;
            $d->name_suffix = $r->name_suffix;
            $d->lname = $r->lname;
            $d->fname = $r->fname;
            $d->mname = $r->mname;
            $d->bdate = $r->bdate;
            $d->gender = $r->gender;
            $d->civil_stat = $r->civil_stat;
            $d->tel_no = $r->tel_no;
            $d->mobile_no = $r->mobile_no;
            $d->email = $r->email;
            $d->nationality = $r->nationality;
            $d->occupation = $r->occupation;
            $d->home_no_st_blk = $r->home_no_st_blk;
            $d->home_brgy = $r->home_brgy;
            $d->home_citymun = $r->home_citymun;
            $d->home_prov = $r->home_prov;
            $d->home_region = $r->home_region;
            $d->home_zip = $r->home_zip;
            $d->office_no_st_blk = $r->office_no_st_blk;
            $d->office_brgy = $r->office_brgy;
            $d->office_citymun = $r->office_citymun;
            $d->office_prov = $r->office_prov;
            $d->office_region = $r->office_region;
            $d->office_zip = $r->office_zip;
            $d->donation_stat = $r->donation_stat;
            $d->donor_stat = $r->donor_stat;
            $d->deferral_basis = $r->deferral_basis;
            $d->facility_cd = $r->facility_cd;
            $d->lfinger = $r->lfinger;
            $d->rfinger = $r->rfinger;
            $d->created_by = $r->created_by;
            $d->created_dt = $r->created_dt;
            $d->updated_by = $r->updated_by;
            $d->updated_dt = $r->updated_dt;
            $d->save();
        }

        return ['status' => 'ok'];
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
