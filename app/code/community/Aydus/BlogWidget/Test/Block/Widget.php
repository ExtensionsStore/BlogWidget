<?php 

/**
 * Widget test
 *
 * @category   Aydus
 * @package    Aydus_BlogWidget
 * @author     Aydus <davidt@aydus.com>
 */

class Aydus_BlogWidget_Test_Block_Widget extends EcomDev_PHPUnit_Test_Case_Controller 
{	
    /**
     * 
     * @test 
     * @loadFixture
     */
    public function getItems()
    {
        echo "\nAydus_BlogWidget block test started.";
        
        Mage::getDesign()->setArea('frontend')->setPackageName('base')->setTheme('default');
        
        $this->dispatch('');
        $this->assertLayoutHandleLoaded('default');
        $this->assertLayoutBlockCreated('footer');
        $this->assertLayoutBlockCreated('bottom.container');
        
        //$layout = $this->app()->getLayout();
        
        $this->assertLayoutBlockCreated('84521362368a7a46a0b53cfcc6b8abea');
        
        echo "\nAydus_BlogWidget block test completed.";
    }
	
}