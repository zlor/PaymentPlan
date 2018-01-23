<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/20
 * Time: 下午4:16
 */

namespace App\Models\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;

trait UploadFileTool
{
    /**
     * Upload directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * File name.
     *
     * @var null
     */
    protected $file_name = null;


    /**
     * File need rename.
     *
     * @var null
     */
    protected $needRename = false;

    /**
     * Storage instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $storage = '';

    /**
     * Storage file type
     *
     * @var string
     */
    protected $file_type = '';


    /**
     * 设置存储盘标识
     *
     * @param $disk
     *
     * @return $this|void
     */
    public function disk($disk)
    {
        if (!array_key_exists($disk, config('filesystems.disks'))) {
            $error = new MessageBag([
                'title'   => __('config_error', '', 'message'),
                'message' => __('config_error_msg', ['&disk&'=>$disk],'message'),
            ]);

            return session()->flash('error', $error);
        }

        $this->storage = Storage::disk($disk);

        return $this;
    }

    /**
     * Upload file and delete original file.
     *
     * @param UploadedFile $file
     *
     * @return mixed
     */
    protected function upload(UploadedFile $file)
    {
        $this->renameIfExists($file);

        $target = $this->getDirectory().'/'.$this->file_name;

        $res = $this->storage->put($target, file_get_contents($file->getRealPath()));

        return $target;
    }

    /**
     * 设置需要 命名
     * @param $name
     *
     * @return $this
     */
    public function rename()
    {
        $this->needRename = true;

        return $this;
    }

    /**
     * If name already exists, rename it.
     *
     * @param $file
     *
     * @return void
     */
    public function renameIfExists(UploadedFile $file)
    {
        if ($this->storage->exists("{$this->getDirectory()}/$this->file_name"))
        {
            $this->file_name = $this->generateUniqueName($file);
        }
    }

    /**
     * Get directory for store file.
     *
     * @return mixed|string
     */
    public function getDirectory()
    {
        if ($this->directory instanceof \Closure) {
            return call_user_func($this->directory, $this);
        }

        return $this->directory ?: $this->defaultDirectory();
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.import.directory.'.$this->file_type);
    }


    /**
     * Specify the directory and name for upload file.
     *
     * @param string      $directory
     * @param null|string $name
     *
     * @return $this
     */
    public function move($directory, $name = null)
    {
        $this->dir($directory);

        $this->name($name);

        return $this;
    }

    /**
     * Specify the directory upload file.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function dir($dir)
    {
        if ($dir) {
            $this->directory = $dir;
        }

        return $this;
    }

    /**
     * Set name of store name.
     *
     * @param string|callable $name
     *
     * @return $this
     */
    public function name($name)
    {
        if ($name) {
            $this->file_name = $name;
        }

        return $this;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @param Integer $repeatNum
     *
     * @return string
     */
    protected function generateUniqueName(UploadedFile $file, $repeatNum = 1)
    {
        $filename = $this->withoutExtension(strlen($file->extension())).'_'.$repeatNum.'.'.$file->extension();
        // 计算出重复的次数
        if($this->storage->exists("{$this->getDirectory()}/{$filename}"))
        {
            return $this->generateUniqueName($file, $repeatNum + 1);
        }else{
            return $filename;
        }
    }

    /**
     * 返回不带扩展名的文件名
     *
     * @param int $extLen
     *
     * @return string
     */
    protected function withoutExtension($extLen = 0)
    {
        return substr($this->file_name, 0, -1 * ($extLen + 1) );
    }
}