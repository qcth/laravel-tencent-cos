<?php

namespace Qcth\TencentCos\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * 删除指定账号下的空存储桶。
 * 腾讯手册  https://cloud.tencent.com/document/product/436/34277
 * Class CreateBucket
 * @package Qcth\TencentCos\Plugins
 */
class DeleteBucket extends AbstractPlugin
{
	// 示例Storage::disk('cosv5')->DeleteBucket($bucket_name)
	public function getMethod()
	{
		//这个返出的名字，就是外面调用时的方法名
		return 'DeleteBucket';
	}
	//外面调用上面返出的方法名时，执行此方法
	public function handle($bucket_name)
	{
		//调用驱动类(Adapter.php)里的  DeleteBucket方法
		return $this->filesystem->getAdapter()->DeleteBucket($bucket_name);
	}
}
