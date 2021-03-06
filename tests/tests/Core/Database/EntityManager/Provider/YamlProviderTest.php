<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\EntityManager\Provider\YamlProvider;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerYaml;
use Concrete\Tests\Core\Database\Traits\DirectoryHelpers;

/**
 * YamlProviderTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class YamlProviderTest extends \PHPUnit_Framework_TestCase
{
    
    use DirectoryHelpers;
    
    /**
     * Stub of a package controller
     * 
     * @var PackageControllerYaml
     */
    private $packageStub;
    
    /**
     * Setup
     */
    public function setUp()
    {
        $this->app = Application::getFacadeApplication();
        $this->packageStub = new PackageControllerYaml($this->app);
        parent::setUp();
    }
    
    /**
     * Test default mapping location and namespace for YamlProvidor
     */
    public function testGetDriversDefaultBehaviourSuccess()
    {
        $yamlProvider = new YamlProvider($this->packageStub);
        
        $drivers = $yamlProvider->getDrivers();
        // get c5 driver
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        // get Doctrine driver
        $driver = $c5Driver->getDriver();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\YamlDriver',$driver);
        $driverPaths = $driver->getLocator()->getPaths();
        $shortenedPath = $this->folderPathCleaner($driverPaths[0]);
        $this->assertEquals('config/yaml', $shortenedPath);
        $driverNamespace = $c5Driver->getNamespace();
        $this->assertEquals('Concrete\Package\TestMetadatadriverYaml\Entity', $driverNamespace);
    }
    
    /**
     * Test custom mapping location and namespace for YamlProvider
     */
    public function testGetDriversAddManuallyLocationAndNamespace(){
        $yamlProvider = new YamlProvider($this->packageStub, false);
        $namespace = 'MyNamespace\Some\Foo';
        $locations = array('mapping/yaml', 'mapping/test/yaml');
        $yamlProvider->addDriver($namespace, $locations);
        
        $drivers = $yamlProvider->getDrivers();
        // get c5 driver
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        // get Doctrine driver
        $driver = $c5Driver->getDriver();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\YamlDriver',$driver);
        $driverPaths = $driver->getLocator()->getPaths();
        $shortenedPath = $this->folderPathCleaner($driverPaths[0]);
        $this->assertEquals($locations[0], $shortenedPath);
        $driverNamespace = $c5Driver->getNamespace();
        $this->assertEquals($namespace, $driverNamespace);
    }
}
