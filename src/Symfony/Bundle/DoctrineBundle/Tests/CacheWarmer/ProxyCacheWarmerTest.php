<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\DoctrineBundle\Tests\CacheWarmer;

use Symfony\Bundle\DoctrineBundle\CacheWarmer\ProxyCacheWarmer;

class ProxyCacheWarmerTest extends \Symfony\Bundle\DoctrineBundle\Tests\TestCase
{
    /**
     * This is not necessarily a good test, it doesnt generate any proxies
     * because there are none in the AnnotationsBundle. However that is
     * rather a task of doctrine to test. We touch the lines here and
     * verify that the container is called correctly for the relevant information.
     */
    public function testWarmCache()
    {
        $testManager = $this->createTestEntityManager(array(
            __DIR__ . "/../DependencyInjection/Fixtures/Bundles/AnnotationsBundle/Entity")
        );
        $container = $this->getMock('Symfony\Component\DependencyInjection\Container');
        $container->expects($this->at(0))
                  ->method('getParameter')
                  ->with($this->equalTo('doctrine.orm.entity_managers'))
                  ->will($this->returnValue(array('default', 'foo')));
        $container->expects($this->at(1))
                  ->method('get')
                  ->with($this->equalTo('doctrine.orm.default_entity_manager'))
                  ->will($this->returnValue($testManager));
        $container->expects($this->at(2))
                  ->method('get')
                  ->with($this->equalTo('doctrine.orm.foo_entity_manager'))
                  ->will($this->returnValue($testManager));

        $cacheWarmer = new ProxyCacheWarmer($container);
        $cacheWarmer->warmUp(sys_get_temp_dir());
    }

    public function testProxyCacheWarmingIsNotOptional()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\Container');
        $cacheWarmer = new ProxyCacheWarmer($container);

        $this->assertFalse($cacheWarmer->isOptional());
    }
}