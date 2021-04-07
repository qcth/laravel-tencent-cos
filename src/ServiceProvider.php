<?php

namespace Qcth\TencentCos;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


use League\Flysystem\Filesystem;
use Qcloud\Cos\Client;
use Qcth\TencentCos\Plugins\CreateBucket;
use Qcth\TencentCos\Plugins\DeleteBucket;
use Qcth\TencentCos\Plugins\HeadBucket;
use Qcth\TencentCos\Plugins\ListBuckets;
use Qcth\TencentCos\Plugins\OtherMethod;

/**
 * 腾讯cosv5服务提供者,以插件形式注入
 * Class ServiceProvider
 * @package Qcth\TencentCos
 */
class ServiceProvider extends LaravelServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

		$this->app->make('filesystem')
			->extend('cosv5', function ($app, $config) {

				//腾讯cos对象存储的实例
				$Client=new Client($config);
				//必须返出此对象
				$file_system= new Filesystem(new Adapter($Client,$config));

				//查询所有存储桶
				$ListBuckets= new ListBuckets();
				//创建存储桶
				$CreateBucket=new CreateBucket();
				//确认 Bucket 是否存在且是否有权限访问
				$HeadBucket=new HeadBucket();
				//删除一个存储桶
				$DeleteBucket=new DeleteBucket();
				//调用非继承的18个以外的方法
				$OtherMethod=new OtherMethod();

				$file_system->addPlugin($ListBuckets);
				$file_system->addPlugin($CreateBucket);
				$file_system->addPlugin($HeadBucket);
				$file_system->addPlugin($DeleteBucket);
				$file_system->addPlugin($OtherMethod);

				return $file_system;
			});
    }

}
