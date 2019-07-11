<?php

namespace App\Http\Controllers\Shared;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use Intervention\Image\ImageManagerStatic as Image;

use App\Http\Traits\ResponseUtilities;

use Exception;

class ImagesController extends Controller
{
    use ResponseUtilities;

    private $data;
    private $baseURL;
    private $threshold;

    public function __construct(){

        $this->data = [
            "code"=> null,
            "message"=>"",
            "data" => new \stdClass()
        ];

        $this->baseURL = \URL::to('/')."/storage/users_avatar/";
        $this->threshold = 1024*1000;
    }

    public function store(StoreImageRequest $request)
    {
        $this->data['code'] = 400;
        $this->data['message'] = __('messages.uploading_failed');

        try{
            if($request->hasFile('image')) {

                //Get file name with the extension
                $fileNameWithExt = $request->file('image')->getClientOriginalName();
                //get just file name
                $fileName=pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                //Get just extension
                $extension=$request->file('image')->getClientOriginalExtension();
                //file name to store
                $uniqueFileName=$fileName.'_'.time().'.'.$extension;

                // $path=$request->file('image')->storeAs('public/users_avatar', $uniqueFileName);
                $image = Image::make($request->file('image')->getRealPath());
                $image->save('storage/users_avatar/'.$uniqueFileName,
                            $this->getPercentageToMaxQuality($image->filesize(), $this->threshold));

                $this->data['code'] = 200;
                $this->data['message'] = __('messages.uploading_success');
                $this->data['data'] = ['image'=>$this->baseURL.$uniqueFileName];

            }
        }catch(Exception $e){
            $this->initErrorResponse($e);
        }
        return response()->json($this->data, 200);
    }

    /**
     * This function returns the best quality percentage to get an image size <= $threshold
     */
    protected function getPercentageToMaxQuality($currQuality, $threshold){
        if($currQuality>$threshold){
            return 100-(int)((($currQuality-$threshold)/$currQuality)*100);
        }
        return 100;
    }
}