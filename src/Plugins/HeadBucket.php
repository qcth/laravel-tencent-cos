<?php

namespace Qcth\TencentCos\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * 确认 Bucket 是否存在且是否有权限访问
 * 腾讯手册  https://cloud.tencent.com/document/product/436/34277
 * Class CreateBucket
 * @package Qcth\TencentCos\Plugins
 */
class HeadBucket extends AbstractPlugin
{
	// 示例Storage::disk('cosv5')->HeadBucket()
	public function getMethod()
	{
		//这个返出的名字，就是外面调用时的方法名
		return 'HeadBucket';
	}
	//外面调用上面返出的方法名时，执行此方法
	public function handle($bucket_name)
	{
		//调用驱动类(Adapter.php)里的  HeadBucket
		return $this->filesystem->getAdapter()->HeadBucket($bucket_name);
	}
}
