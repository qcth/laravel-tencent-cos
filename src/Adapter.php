<?php

namespace Qcth\TencentCos;

use Carbon\Carbon;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\CanOverwriteFiles;

use League\Flysystem\Config;

use Qcloud\Cos\Client;
use Qcth\TencentCos\Traits\Tencent;

/**
 * 自定义驱动
 * Class Adapter.
 * 必须要实现的接口 共计 18 个方法
 */
class Adapter extends AbstractAdapter implements CanOverwriteFiles
{
	use Tencent;

	//腾讯cos对象
	protected $client;
	//cos配置项 此数组是：config/filesystems.php  里 cosv5 对应的数组
	protected $config;

	public function __construct(Client $client, Array $config)
	{
		$this->client = $client;
		$this->config=$config;
	}



	/**
	 * Write a new file.
	 * 把内容写成一个文件
	 * @param string $path
	 * @param string $contents
	 * @param Config $config   Config object
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function write($path, $contents, Config $config){

		$t_data=$this->upload($path,$contents);
		if($t_data['code']!=0){
			return false;
		}
		return true;
	}

	/**
	 * Write a new file using a stream.
	 *
	 * @param string   $path
	 * @param resource $resource
	 * @param Config   $config   Config object
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function writeStream($path, $resource, Config $config){

		$t_data=$this->upload($path,$resource);
		if($t_data['code']!=0){
			return false;
		}
		return true;
	}

	/**
	 * Update a file.
	 *
	 * @param string $path
	 * @param string $contents
	 * @param Config $config   Config object
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function update($path, $contents, Config $config){
		return $this->write($path, $contents,$config);
	}

	/**
	 * Update a file using a stream.
	 *
	 * @param string   $path
	 * @param resource $resource
	 * @param Config   $config   Config object
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function updateStream($path, $resource, Config $config){
		return $this->writeStream($path, $resource, $config);
	}

	/**
	 * Rename a file.
	 *
	 * @param string $path
	 * @param string $newpath
	 *
	 * @return bool
	 */
	public function rename($path, $newpath){

		$copy_data=$this->copy($path, $newpath);
		//复制失败
		if(!$copy_data){
			return false;
		}
		//删除旧文件
		return $this->delete($path);

	}

	/**
	 * Copy a file.
	 *
	 * @param string $path
	 * @param string $newpath
	 *
	 * @return bool
	 */
	public function copy($path, $newpath){

		$t_data=$this->tx_copy($newpath,$path);
		if($t_data['code']!=0){
			return false;
		}
		return true;
	}

	/**
	 * Delete a file.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	public function delete($path){

		$t_data=$this->deleteObject(['Key'=>$path]);

		if($t_data['code']!=0){
			return false;
		}
		return true;
	}

	/**
	 * Delete a directory.
	 *
	 * @param string $dirname
	 *
	 * @return bool
	 */
	public function deleteDir($dirname){

		/*
		 * 每次默认返回的最大条目数为 1000 条，如果无法一次返回所有的 list，则返回结果中的 IsTruncated 为 true，同时会附加一个 NextMarker 字段，提示下一个条目的起点。若一次请求，已经返回了整个 list，则不会有 NextMarker 这个字段，同时 IsTruncated 为 false。

		*  若把 prefix 设置为某个文件夹的全路径名，则可以列出以此 prefix 为开头的文件，即该文件夹下递归的所有文件和子文件夹。如果再设置 delimiter 定界符为 “/”，则只列出该文件夹下的文件，子文件夹下递归的文件和文件夹名将不被列出。而子文件夹名将会以 CommonPrefix 的形式给出。
		*/


		//把此目录下的所有文件，遍历出来，全部删除
		$select_array=[
			'Delimiter' => '',  //目录分隔符，为空取所有包括子目录下的文件；如果为 /  则取当前目录下的文件，不包括子目录下的文件
			'Prefix' => $dirname.'/',  //  此处一定要拼接上 /  要不然 删除的时候，会删除目录及当前目前同名的文件；例如，删除 a目录，如果不拼装斜扛的话，也会删除 a.jpg文件
			'MaxKeys' => 1000,
		];
		$list_result=$this->listObjects($select_array);

		if($list_result['code']!=0){
			return false;
		}

		//此目录下没有文件
		if(empty($list_result['t_data']['Contents'])){
			return false;
		}

		//组装要删除的文件
		$delete_files=['Objects'=>[]];
		foreach ($list_result['t_data']['Contents'] as $k=>$v){
			$delete_files['Objects'][]['Key']=$v['Key'];
		}

		//批量删除文件
		$del_result=$this->deleteObjects($delete_files);

		//删除失败
		if($del_result['code']!=0){
			return  false;
		}

		//代表没有取完, 递归找文件，再删除
		if($list_result['t_data']['IsTruncated']){
			return $this->deleteDir($dirname);
		}

		return true;

	}

	/**
	 * Create a directory.
	 *
	 * @param string $dirname directory name
	 * @param Config $config
	 *
	 * @return array|false
	 */
	public function createDir($dirname, Config $config){

		//调用简单上传对象，内容为空
		$array=[
			'Key'=>$dirname.'/',
			'Body'=>''
		];
		//上传内容为空的对象
		$create_result=$this->putObject($array);
		//上传错误
		if($create_result['code']!=0){
			return false;
		}
		return true;
	}

	/**
	 * 设置对象（文件）的可见性(权限)
	 * $visibility 能传的值为 public  private  null 可以绕过 laravel，如果传其它值，laravel会报错
	 * Set the visibility for a file.
	 *
	 * @param string $path
	 * @param string $visibility
	 *
	 * @return array|false file meta data
	 */
	public function setVisibility($path, $visibility){

		//赋值为 default
		if(empty($visibility)){
			$visibility='default';
		}
		//赋值为 public-read
		if(strtolower($visibility)=='public'){
			$visibility='public-read';
		}

		//腾讯cos 对象存储，只认识此三种权限
		$acl=['default','private','public-read'];

		//不支持设置其它的权限
		if(!in_array($visibility,$acl)){
			return false;
		}

		$array=[
			'Key'=>$path,
			'ACL' => $visibility,  //default 继承存储桶权限   private 私有读取 、 public-read 公有读，私有写
		];
		//设置权限
		$t_data=$this->putObjectAcl($array);

		if($t_data['code']!=0){
			return false;
		}

		return true;
	}

	/**
	 * Check whether a file exists.
	 * 判断一个文件或目录 是否存在，存在返出true ; 不存在 返出 false
	 * @param string $path
	 *
	 * @return array|bool|null
	 */
	public function has($path){

		return (boolean)$this->getMetadata($path);
	}

	/**
	 * 读一个文件，返出文件内容
	 * Read a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function read($path){

		//获取对象内容
		$result_data=$this->getObject(['Key'=>$path]);

		if($result_data['code']!=0){
			return  false;
		}

		//把流对象转成 字符串
		$str=(string)$result_data['t_data']['Body'];

		return  ['contents'=>$str];
	}

	/**
	 * 读一个文件，返出此文件的流资源
	 * Read a file as a stream.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function readStream($path){

		//获取对象内容
		$result_data=$this->getObject(['Key'=>$path]);

		if($result_data['code']!=0){
			return  false;
		}

		//流对象
		$stream_obj=$result_data['t_data']['Body'];

		//转成流资源
		$stream_resource=$stream_obj->detach();

		//$stream_str=stream_get_contents($stream_obj); // 把流资源转成字符串，则用这个

		return  ['stream'=>$stream_resource];
	}

	/**
	 * 列出目录下的文件
	 * List contents of a directory.
	 *
	 * @param string $directory
	 * @param bool   $recursive
	 *
	 * @return array
	 */
	public function listContents($directory = '', $recursive = false){
		return  $this->_file_list($directory,$recursive,'',true);
	}

	//文件列表
	private function _file_list($directory='',$recursive=false,$Marker='',$init=false){
		//静态变量
		static $list=[];
		//初始化
		if($init){
			$list=[];
		}
		//取全部，包括子目录下的文件
		$Delimiter='';
		if(!$recursive){
			//取当前目录下的文件,不包括子目录下的文件
			$Delimiter='/';
		}
		$select_array=[
			'Delimiter' => $Delimiter,  //目录分隔符，为空取所有包括子目录下的文件；如果为 /  则取当前目录下的文件，不包括子目录下的文件
			'Prefix' => $directory.'/',  //  此处一定要拼接上 /  要不然 查找的时候，会查找目录及当前目前同名的文件；例如，查找 a目录，如果不拼装斜扛的话，也会查找 a.jpg文件
			'MaxKeys' => 1000,      //每次取1000条
			'Marker'=>$Marker    //起始位置，分页用
		];
		$list_result=$this->listObjects($select_array);
		if($list_result['code']!=0){
			return $list;
		}

		//压入到列表数组里
		foreach ($list_result['t_data']['Contents'] as $v){
			$path = pathinfo($v['Key']);

			$list[]=[
				'type'=>substr($v['Key'], -1) === '/' ? 'dir' : 'file',
				'path'=>$v['Key'],
				'timestamp'=>Carbon::parse($v['LastModified'])->getTimestamp(),
				'size'=>(int)$v['Size'],
				'dirname'   => (string) $path['dirname'],
				'basename'  => (string) $path['basename'],
				'extension' => !empty($path['extension']) ? $path['extension'] : '',
				'filename'  => (string) $path['filename'],
			];

		}

		//代表没有取完, 递归找文件
		if($list_result['t_data']['IsTruncated']){
			 $this->_file_list($directory,$recursive,$list_result['t_data']['NextMarker'],false);
		}

		return $list;

	}

	/**
	 * Get all the meta data of a file or directory.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getMetadata($path){

		//如果是目录 连上斜扛； 如果是文件，则不加扛，这样做的目的是：连目录也要检查是否存在了
		$path= empty(pathinfo($path)['extension']) ? $path.'/' : $path;

		//调用腾讯对象存储接口，检查 查询 Object 的 Meta 信息
		$result_data=$this->headObject(['Key'=>$path]);

		if($result_data['code']!=0){
			return false;
		}
		return $result_data['t_data']->toArray();
	}

	/**
	 * Get the size of a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getSize($path){

		//meta信息中，获取size
		$meta=$this->getMetadata($path);
		if(empty($meta['ContentLength'])){
			return false;
		}

		return ['size'=>$meta['ContentLength']];
	}

	/**
	 * Get the mimetype of a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getMimetype($path){
		//从 meta中获取
		$meta=$this->getMetadata($path);
		if(empty($meta['ContentType'])){
			return false;
		}
		return ['mimetype'=>$meta['ContentType']];
	}

	/**
	 * 获取文件最后修改的时间
	 * Get the last modified time of a file as a timestamp.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getTimestamp($path){

		//从 meta中获取
		$meta=$this->getMetadata($path);

		if(empty($meta['LastModified'])){
			return false;
		}

		return ['timestamp'=>strtotime($meta['LastModified'])];
	}

	/**
	 * 此方法获取 用户权限，不能获取公共权限
	 * https://cloud.tencent.com/document/product/436/30752#.E6.93.8D.E4.BD.9C-permission
	 * 查询对象（文件）的可见性(权限)，此接口，并不能返回 public  private  default 等权限，只供参考，并不实用
	 * 而且在腾讯后台，更改某文件的 公共权限，并不影响此接口
	 * 查询指定对象的访问权限控制列表
	 * Get the visibility of a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getVisibility($path){

		//说明，不能获取 private  public  default 等限权
		$result_data=$this->getObjectAcl(['Key'=>$path]);

		foreach ($result_data['t_data']['Grants'] as $k=>$v){

			if($v['Grant']['Permission']=='FULL_CONTROL'){  //完全控制，暂且作为 public

				return ['visibility' => 'public'];
			}
		}

		return ['visibility' => 'private'];


	}




}
