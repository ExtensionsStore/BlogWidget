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
                
                    $title = (string)$feed->channel->title;
                    $this->setBlockTitle($title);
                }   
                
                $items = $feed->channel->item;
                
                foreach ($items as $i=>$itemElement){
                    
                    if ($i>=100){
                        break;
                    }
                    
                    $data = (array)$itemElement;
                    $namespaces = $itemElement->getNameSpaces(true);
                    if (isset($namespaces['media'])){
                        $media = $itemElement->children($namespaces['media']);
                        $thumbnail = $media->thumbnail;
                        $image = $thumbnail->attributes();
                        $data['thumbnail'] = $image['url'];
                    }
                    
                    $item = new Varien_Object();
                    $item->setData($data);
                    
                    $this->_items[] = $item;
                }
                
                $orderBy = $this->getOrderBy();
                
                $orderSort = function($a, $b) use ($orderBy)
                {                    
                    if ($orderBy == 'pubDate'){
                        
                        $aVal = strtotime($a->getData('pubDate'));
                        $bVal = strtotime($b->getData('pubDate'));
                        $return = ($aVal > $bVal) ? -1 : 1;
                        
                    } else {
                        
                        $aVal = $a->getTitle();
                        $bVal = $b->getTitle();
                        
                        $return = strnatcasecmp($this->handleArticles($aVal),$this->handleArticles($bVal));
                    }

                    return $return;
                };

                usort($this->_items, $orderSort);    
                
            }
            
        }
        
        return $this->_items;
    }
    
    /**
     * @see http://stackoverflow.com/questions/20744995/how-to-sort-an-array-with-php-ignoring-the-articles-a-an-the-in-the-beginni
     * @param string $str
     * @return string
     */
    public function handleArticles($str) {
        list($first,$rest) = explode(" ",$str." ",2);
           // the extra space is to prevent "undefined offset" notices
           // on single-word titles
        $validarticles = array("a","an","the");
        if( in_array(strtolower($first),$validarticles)) return $rest.", ".$first;
        return $str;
    } 
	
}