<?php
namespace Qcth\TencentCos\Traits;

/**
 * Trait Tencent
 * 腾讯对象操作方法
 * @package Qcth\TencentCos\Traits
 *
 * 注意 Delimiter' => '',  //目录分隔符，为空取所有包括子目录下的文件；如果为 /  则取当前目录下的文件，不包括子目录下的文件
 */
trait Tencent{

	//查询某账号下所有存储桶
	public function ListBuckets(){
		try {
			$data=$this->client->listBuckets();
			return ['code'=>0,'msg'=>'OK','t_data'=>$data];
		}catch (\Exception $e){
			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	//在指定账号下创建一个存储桶
	public function CreateBucket($bucket_name=null){

		//存储桶名字不能为空
		if(empty($bucket_name)){
			return ['code'=>1,'msg'=>'存储桶名字不能为空'];
		}

		//config配置项，cos5元素 里appId不能为空
		if(empty($this->config['credentials']['appId'])){
			return ['code'=>1,'msg'=>'appId不能为空'];
		}

		$bucket=$bucket_name.'-'.$this->config['credentials']['appId'];

		try {
			$t_data=$this->client->createBucket(['Bucket'=>$bucket]);

			return ['code'=>0,'msg'=>'OK','t_data'=>$t_data];
		}catch (\Exception $e){
			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	//确认 Bucket 是否存在且是否有权限访问
	public function HeadBucket($bucket_name=null){

		//存储桶名字不能为空
		if(empty($bucket_name)){
			return ['code'=>1,'msg'=>'存储桶名字不能为空'];
		}

		//config配置项，cos5元素 里appId不能为空
		if(empty($this->config['credentials']['appId'])){
			return ['code'=>1,'msg'=>'appId不能为空'];
		}

		$bucket=$bucket_name.'-'.$this->config['credentials']['appId'];

		try {
			$t_data=$this->client->headBucket(['Bucket'=>$bucket]);

			return ['code'=>0,'msg'=>'OK','t_data'=>$t_data];
		}catch (\Exception $e){
			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	//删除指定账号下的空存储桶
	public function DeleteBucket($bucket_name=null){

		//存储桶名字不能为空
		if(empty($bucket_name)){
			return ['code'=>1,'msg'=>'存储桶名字不能为空'];
		}

		//config配置项，cos5元素 里appId不能为空
		if(empty($this->config['credentials']['appId'])){
			return ['code'=>1,'msg'=>'appId不能为空'];
		}

		$bucket=$bucket_name.'-'.$this->config['credentials']['appId'];

		try {
			$t_data=$this->client->deleteBucket(['Bucket'=>$bucket]);

			return ['code'=>0,'msg'=>'OK','t_data'=>$t_data];
		}catch (\Exception $e){
			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/**
	 * 查询对象列表
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function listObjects(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->listObjects($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/**
	 * 查询存储桶下的部分或者全部对象及其历史版本信息
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function listObjectVersions(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->listObjectVersions($params)] ;

		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 简单上传对象
	 * 上传对象到指定的存储桶中（PUT Object），最大支持上传不超过5GB的对象，5GB以上对象请使用 分块上传 或 高级接口 上传。
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function putObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->putObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 查询 Object 的 Meta 信息
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function headObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->headObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 下载对象，或 获取对象内容
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function getObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->getObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 将一个对象复制到目标路径
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function copyObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->copyObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/**
	 *  删除单个对象
	 * 在存储桶中删除指定 Object （文件/对象）。
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function deleteObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->deleteObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 批量删除对象
	 * 在存储桶中批量删除 Object （文件/对象）
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function deleteObjects(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->deleteObjects($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	/**
	 * 将归档类型的对象取回访问
	 * 归档文件是不允许浏览器通过网址访问的，必须先取回后，
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function restoreObject(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->restoreObject($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}


	/**
	 * 复合上传
	 * 经测试 大文件上传 提示 curl 超时，后期研究处理,小文件上传没有问题
	 * 如果$body 为空的话，可以创建目录
	 * 该接口内部会根据文件大小，对小文件调用简单上传接口，对大文件调用分块上传接口
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function upload($key,$body,$options=[],$bucket='')
	{
		try {
			if(empty($key)){
				return ['code'=>1,'msg'=>'key不能为空'] ;
			}

			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($bucket)){
				$bucket=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$bucket=$bucket.'-'.$this->config['credentials']['appId'];
			}

			$t_data=$this->client->Upload($bucket,$key,$body);

			return ['code'=>0,'msg'=>'OK','t_data'=>$t_data] ;

		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/**
	 * 复合复制 , 此方法就是腾讯对象存储的 copy方法
	 * 该接口内部会根据文件大小，对小文件调用设置对象复制接口，对大文件调用分块复制接口
	 * 手册 https://cloud.tencent.com/document/product/436/34282
	 */
	public function tx_copy($new_key,$old_key,$options=[],$bucket='')
	{
		try {
			if(empty($new_key)){
				return ['code'=>1,'msg'=>'key不能为空'] ;
			}

			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($bucket)){
				$bucket=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$bucket=$bucket.'-'.$this->config['credentials']['appId'];
			}

			$copySorce=[
				'Region' => $this->config['region'],
				'Bucket' => $bucket,
				'Key' => $old_key,
			];

			$t_data=$this->client->Copy($bucket,$new_key,$copySorce,$options);

			return ['code'=>0,'msg'=>'OK','t_data'=>$t_data] ;

		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/*---------------- 分块操作方法 开始 -------------------------------------------------------*/

	//分块操作流程, 经测试，无法完成分块，提示 上传的分块文件太小
	//分块上传对象： 1 初始化分块上传; 2上传所有分块， 3 完成分块上传后，才能正常访问此文件,未完成的，在分片中，不能操作
	//分块续传：查询已上传块， 上传分块，完成分块上传。
	//删除已上传分块。


	//查询指定存储桶中正在进行的分块上传
	public function listMultipartUploads(Array $params=[]){
		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->listMultipartUploads($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}
	}

	//分块初始化
	public function createMultipartUpload(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->createMultipartUpload($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	//分块上传文件
	public function uploadPart(Array $params=[]){
		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			$uploadPart=$this->client->uploadPart($params);

			return ['code'=>0,'msg'=>'OK','t_data'=>$uploadPart] ;

		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}
	}

	//查询特定分块上传操作中的已上传的块
	public function listParts(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->listParts($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	//完成整个文件的分块上传
	public function completeMultipartUpload(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->completeMultipartUpload($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	//将其他对象复制为一个分块
	public function uploadPartCopy(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->uploadPartCopy($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}
	//终止一个分块上传操作并删除已上传的块
	public function abortMultipartUpload(Array $params=[])
	{

		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->abortMultipartUpload($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}

	}

	/*--------------------------------- 分块操作方法 结束 -------------------------------------------------------*/


	/*--------------------------------- 权限设置及查询 start -------------------------------------------------------*/

	//查询对象的权限，目前只能查询用户权限，不能查询公共权限
	//手册 https://cloud.tencent.com/document/product/436/41898#.E6.9F.A5.E8.AF.A2.E5.AF.B9.E8.B1.A1-acl
	public function getObjectAcl(Array $params=[]){
		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->getObjectAcl($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}
	}

	//设置对象权限
	public function putObjectAcl(Array $params=[]){
		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($params['Bucket'])){
				$params['Bucket']=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$params['Bucket']=$params['Bucket'].'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->putObjectAcl($params)] ;
		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}
	}

	/*--------------------------------- 权限设置及查询 end -------------------------------------------------------*/




	/*--------------------------------- 预签名URL start -------------------------------------------------------*/

	//获取下载签名
	public function getObjectUrl($key,$time='+10 minutes',$bucket=null){
		if(empty($key)){
			return ['code'=>1,'msg'=>'Key也就是path不能为空'];
		}
		try {
			//如果不传，则取配置项的bucket; 否则用自定义的bucket
			if(empty($bucket)){
				$bucket=$this->config['bucket'].'-'.$this->config['credentials']['appId'];
			}else{
				$bucket=$bucket.'-'.$this->config['credentials']['appId'];
			}

			return ['code'=>0,'msg'=>'OK','t_data'=>$this->client->getObjectUrl($bucket,$key,$time)] ;

		}catch (\Exception $e){

			return ['code'=>1,'msg'=>$e->getMessage(),'t_data'=>$e];
		}
	}
}
