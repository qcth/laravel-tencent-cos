<?php

namespace Qcth\TencentCos\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * 在指定账号下创建一个存储桶
 * 腾讯手册  https://cloud.tencent.com/document/product/436/34277
 * Class CreateBucket
 * @package Qcth\TencentCos\Plugins
 */
class CreateBucket extends AbstractPlugin
{
	// 示例Storage::disk('cosv5')->CreateBucket()
	public function getMethod()
	{
		//这个返出的名字，就是外面调用时的方法名
		return 'CreateBucket';
	}
	//外面调用上面返出的方法名时，执行此方法
	public function handle($bucket_name)
	{
		//调用驱动类(Adapter.php)里的  CreateBucket方法
		return $this->filesystem->getAdapter()->CreateBucket($bucket_name);
	}
}
