<?php

namespace App\Jobs;

use App\Models\Facility;
use App\Models\DocumentType;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
class UploadFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $payload;
    public function __construct($_payload)
    {

        $this->payload=$_payload;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            if($this->payload['delete_previous']){
             $this->destroy($this->payload);
            }

            $s3 = \Storage::disk('s3');

            $filename = $this->normalizeFilename($this->payload['file_name']);
            $date_append = date("Y-m-d-His-");
            $folder = env("FILE_UPLOAD_PATH", ""); // storage_path('uploads');


            $s3->put($folder . $date_append . $filename,base64_decode($this->payload['file_content']), 'public');


            if ($s3->exists($folder . $date_append . $filename)) {

                $resource_url=$s3->url($folder . $date_append . $filename);

                $this->payload['path']=  $resource_url;
                $this->payload['file_name']=$date_append .$filename;
                Upload::create($this->payload);
            }
        } catch (\Exception $e) {
           \Log::critical($e);
        }
    }
    public  function normalizeFilename ($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }
    /**
     * Remove the specified Upload from storage.
     * DELETE /uploads/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public static function destroy($payload)
    {

            /** @var Upload $upload */
            $upload = Upload::where('entity',$payload['entity'])->first();

            if (empty($upload)) {
            \Log::critical('Upload not found');
            }else{
                if(File::exists(storage_path('app/'.$upload->path)))
                unlink(storage_path('app/'.$upload->path));


                $s3 = \Storage::disk('s3');
                $main_image=env("FILE_UPLOAD_PATH", "").basename($upload->path);
                if($s3->exists($main_image)) {//delete image if exist

                    $s3->delete($main_image);
                }
                $upload->delete();
            }



    }

}
