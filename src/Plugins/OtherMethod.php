<?php

namespace Qcth\TencentCos\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * 调用驱动类里，除了继承的18个以外的方法
 * 调用驱动类里的方法，作用是：laravel文件系统，只允许调用重写的18个方法，其它方法不允许调用
 * 示例 $disk=Storage::disk('cosv5')->OtherMethod('调用的方法名','参数一','参数二'.....);
 * Class OtherMethod
 * @package Qcth\TencentCos\Plugins
 */
class OtherMethod extends AbstractPlugin
{
	// 示例Storage::disk('cosv5')->ListBuckets()
	public function getMethod()
	{
		//这个返出的名字，就是外面调用时的方法名
		return 'OtherMethod';
	}
	//外面调用上面返出的方法名时，执行此方法
	public function handle(...$params)
	{
		//要调用的方法名
		$func=$params[0]??'';
		if(empty($func)){
			return ['code'=>1,'msg'=>'要调用的方法名，必传；参数顺序是：第一个是方法名，以后的是此方法的参数'];
		}
		if(!method_exists($this->filesystem->getAdapter(),$func)){
			return ['code'=>1,'msg'=>'要调用的方法:'.$func.'() 不存在，请在相关驱动里定义好后，再尝试调用'];
		}

		//调用驱动类(Adapter.php)里的  其它方法
		return $this->filesystem->getAdapter()->$func(array_slice($params,1));
	}
}
