<?php 

/**
 * Widget block
 *
 * @category   Aydus
 * @package    Aydus_BlogWidget
 * @author     Aydus <davidt@aydus.com>
 */

class Aydus_BlogWidget_Block_Widget extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{	
    
    protected $_template = 'aydus/blogwidget/widget.phtml';
    protected $_items;
        
    public function getItems()
    {
        if (!is_array($this->_items)){
            
            $blogUrl = $this->getBlogUrl();
            
            $feed = simplexml_load_file($blogUrl);
            
            if ($feed && $feed->channel){
                
                if (!$this->getBlockTitle()){
                
                    $title = $feed->channel->title;
                    $this->setBlockTitle($title);
                }   
                
                $items = $feed->channel->item;
                
                foreach ($items as $i=>$item){
                    
                    if ($i>=100){
                        break;
                    }
                    
                    $data = (array)$item;
                    $item = new Varien_Object();
                    $item->setData($data);
                    
                    $this->_items[] = $item;
                }
                
                $orderBy = $this->getOrderBy();
                
                //http://stackoverflow.com/questions/20744995/how-to-sort-an-array-with-php-ignoring-the-articles-a-an-the-in-the-beginni
                function handleArticles($str) {
                    list($first,$rest) = explode(" ",$str." ",2);
                       // the extra space is to prevent "undefined offset" notices
                       // on single-word titles
                    $validarticles = array("a","an","the");
                    if( in_array(strtolower($first),$validarticles)) return $rest.", ".$first;
                    return $str;
                }
                
                $orderSort = function($a, $b) use ($orderBy)
                {                    
                    if ($orderBy == 'pubDate'){
                        
                        $aVal = strtotime($a->getData('pubDate'));
                        $bVal = strtotime($b->getData('pubDate'));
                        $return = ($aVal > $bVal) ? -1 : 1;
                        
                    } else {
                        
                        $aVal = $a->getTitle();
                        $bVal = $b->getTitle();
                        
                        $return = strnatcasecmp(handleArticles($aVal),handleArticles($bVal));
                    }

                    return $return;
                };

                usort($this->_items, $orderSort);    
                
            }
            
        }
        
        return $this->_items;
    }
	
}