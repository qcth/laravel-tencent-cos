<?php

namespace Qcth\TencentCos\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * 查询指定账号下所有的存储桶列表
 * 腾讯手册  https://cloud.tencent.com/document/product/436/34277
 * Class Bucket
 * @package Qcth\TencentCos\Plugins
 */
class ListBuckets extends AbstractPlugin
{
	// 示例Storage::disk('cosv5')->ListBuckets()
	public function getMethod()
	{
		//这个返出的名字，就是外面调用时的方法名
		return 'ListBuckets';
	}
	//外面调用上面返出的方法名时，执行此方法
	public function handle()
	{
		//调用驱动类(Adapter.php)里的  ListBuckets
		return $this->filesystem->getAdapter()->ListBuckets();
	}
}
