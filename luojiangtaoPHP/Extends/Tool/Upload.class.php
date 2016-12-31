<?php
/**
 *
 */
class Upload
{
    //上传文件保存的路径
    private $upload_path =  UPLOAD_PATH;

    public function __construct(){

    }
    /**
     * 上传方法
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:20:32+0800
     * @param    [type]                   $input_name [上传文件input中的name字段]
     * @return   [type]                               [返回文件信息]
     */
    public function upload($input_name)
    {
        if(empty($_FILES[$input_name]['name'])){
            return false;
        }
        $message = '';
        switch ($_FILES[$input_name]['error']) {
            case 4:$message .= '没有文件被上传';
                break;
            case 3:$message .= '文件只有部分被上传';
                break;
            case 2:$message .= '上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值';
                break;
            case 1:$message .= '上传的文件超过了php.ini中upload_max_filesize选项限制的值';
                break;
            case 0:$message .= '上传成功';
                break;
            default:$message .= '未知错误';
        }
        // 文件后缀名
        $ext      = strchr($_FILES[$input_name]['name'], '.');
        // 文件名
        $filename = time() . rand(1,100) . $ext;

        // 没有文件夹则创建
        is_dir($this->upload_path) || mkdir($this->upload_path, 0777, true);

        // 检测上传文件是否合法
        if (!is_uploaded_file($_FILES[$input_name]['tmp_name'])) {
            die('上传文件不合法 not a file');
        }

        // 补全文件名
        $full_name=$this->upload_path.'/'.$filename;
        $file_info = array(
            'filename' => $filename,
            'tmp_name' => $_FILES[$input_name]['tmp_name'],
            'type' => $_FILES[$input_name]['type'],
            'error' => $_FILES[$input_name]['error'],
            'size' => $_FILES[$input_name]['size'],
            'message'  => $message,
            'full_name'  => $full_name,
        );

        // 把文件从临时目录移动到上传目录
        move_uploaded_file($_FILES[$input_name]['tmp_name'], $full_name);
        // 返回文件信息
        return $file_info;
    }

}
