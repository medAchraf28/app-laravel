<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DkimController extends Controller
{
    
	//get DKIM public key

	public function getKey()
        {
	     $dkim_key = Storage::disk('outside')->get('dkim.public.key');
	     $dkim_key = str_replace("-----BEGIN PUBLIC KEY-----","", $dkim_key);
             $dkim_key = str_replace("-----END PUBLIC KEY-----","", $dkim_key);
	
             return $dkim_key;

	}

}
