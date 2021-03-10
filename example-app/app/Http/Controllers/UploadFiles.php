<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadFiles extends Controller
{
   public function __invoke(Request $request) {

       if($request->hasFile('image')){
           $image = $request->file('image');
           $path = "uploaded/" . $image->hashName();
           $content = $image->getContent();
           Storage::disk('images')->put($path, $content);
           Log::info("File {$path} was uploaded");
           return $path;
       }
       Log::alert("File doesn't exist in request");
       return null;

   }
}
