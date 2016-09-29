<?php

namespace OSM\ComCollectBundle\Utils;

use OSM\ComCollectBundle\Entity\Wikipage;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WikipageSource {    
    
    private $feedLink = 'https://wiki.openstreetmap.org/w/api.php?hidebots=1&days=30&limit=500&action=feedrecentchanges&feedformat=rss';
    
    private $options;
    private $optionsResolver;
    
    public function __construct(array $options = []) {
        
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->optionsResolver = $resolver; //TODO: good practice?
    }
    
    public function get() {
        
        $pages = [];
        
        foreach($this->getUpdatedPageNames() as $pageName) {
            
            $page = new Wikipage();
            
            err($pageName);
            
            $page->setName($pageName);
            $page->setContent($this->getPageContent($pageName));
            $page->setCollectedTime(new \DateTime('now'));
            
            $pages[] = $page;
        }
        
        return $pages;
    }
    
    private function getUpdatedPageNames() {
     
        $rss = file_get_contents($this->feedLink);        
        $feed = new \SimpleXmlElement($rss);
        
        $pageNames = [];
        foreach ($feed->channel->item as $entry) { 
            
            //get pubdate of item
            $pubDate = new \DateTime($entry->pubDate);
            $pubDate->setTimeZone(new \DateTimeZone('UTC'));
            
            //only collect unseen entries
            if ($pubDate < $this->options['afterUpdateTime']) {
                
                break;
            }
            
            //only matching pages
            if (!preg_match($this->options['pattern_title'], $entry->title)) {
                
                continue;
            }
            
            //ignore already listed pageLinks
            if (in_array($entry->title->__toString(), $pageNames)) {
            
                continue;
            }
                
            //add pageLink to list
            $pageNames[] = $entry->title->__toString();
            
            //stop collecting after given count
            if ($this->options['limit']-- == 0) {
                
                 break;
            }
        }
        
        return $pageNames;
    }
    
    private function getPageContent($pageName) {
        
        $dom = new \DOMDocument();
        $dom->load($this->getEditPageLink($pageName)); 
        
        $textarea = $dom->getElementsByTagName('textarea');
        
        //no textarea content found
        if (!$textarea->length) {
            
            #TODO: better error management
            return 'OSMCC Error: no textarea Input found';
        }
        
        $content = $textarea->item(0)->nodeValue;
        #$content = explode("\n", $content);
        
        return $content;
    }
    
    private function getEditPageLink($pageName) {
        
        $pageLink = 'https://wiki.openstreetmap.org/w/index.php?title=' . $pageName . '&action=edit';
        
        return $pageLink;
    }

    private function configureOptions(OptionsResolver $resolver) {
        
        $resolver->setDefaults([
          
            'afterUpdateTime' => new \DateTime('01.01.2000'), 
            'pattern_title' => '!.*!', 
            'limit' => 5,
        ]);
        
        $resolver->setAllowedTypes('pattern_title', 'string');
        $resolver->setAllowedTypes('afterUpdateTime', 'DateTime');
        $resolver->setAllowedTypes('limit', 'int');
    }
    
    public function setOptions(array $options = []) {
        
        $this->options = $this->optionsResolver->resolve($options);
        
        return $this;
    }
}
