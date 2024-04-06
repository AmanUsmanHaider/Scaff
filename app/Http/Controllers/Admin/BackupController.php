<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Scalar\String_;
use Ramsey\Uuid\Type\Decimal;

class BackupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admin_user-management_backup-list', ['only' => ['index']]);
        $this->middleware('permission:admin_user-management_backup-create', ['only' => ['create']]);
        $this->middleware('permission:admin_user-management_backup-download', ['only' => ['download']]);
        $this->middleware('permission:admin_user-management_backup-delete', ['only' => ['delete']]);
    }
    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $disk = Storage::disk('local');
        $files = $disk->files(config('backup.backup.name'));
        $backups = [];

        // make an array of backup files, with their filesize and creation date
        foreach ($files as $k => $f) {
            // only take the zip files into account
            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => str_replace(config('backup.backup.name').'/', '', $f),
                    'file_size_byte' => $disk->size($f),
                    'file_size' => $this->humanFilesize($disk->size($f)),
                    'last_modified_timestamp' => $disk->lastModified($f),
                    'date_created' => Carbon::createFromTimestamp($disk->lastModified($f))->isoFormat('llll'),
                    'date_ago' => Carbon::createFromTimestamp($disk->lastModified($f))->diffForHumans(Carbon::now()),
                ];
            }
        }
        $data = array(
            'page_title' => 'Backup',
            'p_title'=>'Backup',
            'data'=>$backups,
            'url'=>route('admin.backup.create'),
            'url_text'=>'Take Backup',
        );
        return view('admin.backup.index')->with($data);
    }
    /**
     * Create Backup
     * @return \Illuminate\Http\Response
     */
    public function create(){
        Artisan::call('backup:run');
        $messages =  [
            array(
                'message' => 'Record created successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.backup.index');
    }
    /**
     * Download Backup
     * @param String_ $file_name
     * @return \Illuminate\Http\Response
     */
    public function download($file_name)
    {
        $disk = Storage::disk('local');
        $file = config('backup.backup.name').'/'.$file_name;

        if ($disk->exists($file)) {
            return Storage::download($file);
        } else {
            abort(404, "The backup file doesn't exist.");
        }
    }
    /**
     * Delete Backup
     * @param String_ $file_name
     * @return \Illuminate\Http\Response
     */
    public function delete($file_name)
    {
        $disk = Storage::disk('local');
        $file = config('backup.backup.name').'/'.$file_name;
        if ($disk->exists($file)) {
            if ($disk->delete($file)){
                $messages =  [
                    array(
                        'message' => 'Record deleted successfully',
                        'message_type' => 'success'
                    ),
                ];
                Session::flash('messages', $messages);
                return redirect()->route('admin.backup.index');
            }
        } else {
            abort(404, "The backup file doesn't exist.");
        }
    }
    /**
     * Human File size understandable
     * @return \Illuminate\Http\Response
     * @param  int_  $size
     * @param  Decimal_  $precision
     */
    function humanFilesize($size, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision).$units[$i];
    }
}
