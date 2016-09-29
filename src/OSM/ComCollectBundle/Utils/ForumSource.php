<?php

namespace OSM\ComCollectBundle\Utils;

class ForumSource
{    
    private $feedLink = 'http://forum.openstreetmap.org/extern.php?action=feed&type=rss';
    
    /**
     * returns postData younger than $lastCollectTime
     * 
     * @param type $lastCollectTime
     * @return type
     */
    public function getNew($lastCollectTime) {
                
        $posts = [];
        foreach ($this->getUpdatedThreadLinks($lastCollectTime) AS $threadLink) { 
        
            $newPosts = $this->getPostDataByThreadLink($threadLink, $lastCollectTime);
                
            $posts = array_merge($posts, $newPosts);
        }
        
        #err($posts); die();
        
        return $posts;
    }
    
    /**
     * returns threadLinks from Forum Feed
     * 
     * @param \DateTime $lastCollectTime
     * @return array
     */
    private function getUpdatedThreadLinks($lastCollectTime) {
        
        $rss = file_get_contents($this->feedLink);        
        $feed = new \SimpleXmlElement($rss);
        
        $links = [];
        foreach ($feed->channel->item as $entry) {
            
            //get pubdate of item
            $pubDate = new \DateTime($entry->pubDate);
            
            //only collect unseen entries
            if ($pubDate < $lastCollectTime) {
                
                break;
            }
            
            $links[] = $entry->link->__toString();
        }
        
        return $links;
    }
    
    /**
     * returns postData younger than $lastCollectTime by given $threadLink
     * 
     * @param string $threadLink
     * @param \DateTime $lastCollectTime
     * @return array
     */
    private function getPostDataByThreadLink($threadLink, $lastCollectTime) {
        
        $html = @file_get_contents($threadLink);
        
        //link is already outdated
        if (!$html) {
             
            return [];
        }
        
        return $this->getDataFromThreadPage($html, $lastCollectTime);
    }
    
    /**
     * crawls the given $html and returns postData younger than $lastCollectTime
     * 
     * @param string $html
     * @param \DateTime $lastCollectTime
     * @return array
     */
    private function getDataFromThreadPage($html, $lastCollectTime) {
        
        $dom = new \DOMDocument();        
        $dom->loadHTML($this->makeHtmlReadable($html));        
        $xpath = new \DOMXPath($dom); 
        $postElements = $xpath->query('//div[contains(@class, "blockpost")]');
                
        $firstPostOnPage = true;
        $posts = [];
        foreach($postElements as $postElement) { 
                       
            $postTime = $this->findTimeInDom($xpath, $postElement);
            $currentPage = $this->findPageNumberInDom($xpath, $postElement); 
            
            //collect all new posts
            if ($postTime > $lastCollectTime) {
                
                $post = $this->getPostDataFromDomNode($xpath, $postElement);
                
                //if first post on page is collectable then check also previous page
                if ($firstPostOnPage AND $currentPage != 1) {
                    
                    $posts = array_merge($posts, $this->getPostsFromPreviousPage($post['threadId'], $currentPage, $lastCollectTime));
                }
                
                $posts[] = $post;
            }
            
            $firstPostOnPage = false;
        }
        
        return $posts;
    }
    
    /**
     * starts a recursive call of getPostDataByThreadLink() of the previous 
     * pages until a post older than $lastCollectTime is found
     * 
     * @param integer $threadId
     * @param integer $currentPage
     * @param \DateTime $lastCollectTime
     * @return array
     */
    private function getPostsFromPreviousPage($threadId, $currentPage, $lastCollectTime) {
        
        $threadLink = 'http://forum.openstreetmap.org/viewtopic.php?id=' . $threadId . '&p=' . ($currentPage-1);
        
        return $this->getPostDataByThreadLink($threadLink, $lastCollectTime);
    }
    
    /**
     * returns the postData from the given postElement
     * 
     * @param \DOMXpath $xpath
     * @param \DOMNode $postElement
     * @return array
     */
    private function getPostDataFromDomNode($xpath, $postElement) {
        
        $post['threadId'] = $this->findThreadIdInDom($xpath, $postElement);
        $post['boardId'] = $this->findBoardIdInDom($xpath, $postElement);
        $post['title'] = $this->findTitleInDom($xpath, $postElement);

        $post['postId'] = $this->findPostIdInDom($xpath, $postElement);
        $post['createdTime'] = $this->findTimeInDom($xpath, $postElement);
        $post['userName'] = $this->findUserNameInDom($xpath, $postElement);
        $post['content'] = $this->findContentInDom($xpath, $postElement);
        $post['number'] = $this->findNumberInDom($xpath, $postElement);
        
        return $post;
    }
    
    /**
     * returns the pageNumber
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return integer
     */
    private function findPageNumberInDom($xpath, $postElement) {
        
        $pageNumber = $xpath->query('//p[contains(@class, "pagelink")]/strong', $postElement)->item(0)->nodeValue;
        
        return $pageNumber;
    }
    
    /**
     * returns the postContent
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return string
     */
    private function findContentInDom($xpath, $postElement) {
        
        $contentElement = $xpath->query('.//div[@class="postmsg"]', $postElement)->item(0);
        
        $content = ""; 
        $children  = $contentElement->childNodes;

        foreach ($children as $child) { 
            
            $content .= $contentElement->ownerDocument->saveHTML($child);
        }

        return trim($content); 
    }
    
    /**
     * returns the postUserName
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return string
     */
    private function findUserNameInDom($xpath, $postElement) {
        
        return $xpath->query('.//div[contains(@class, "postleft")]/dl/dt/strong', $postElement)->item(0)->nodeValue;
    }
    
    /**
     * returns the postNumber
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return integer
     */
    private function findNumberInDom($xpath, $postElement) {
        
        $number = $xpath->query('.//h2//span[@class="conr"]', $postElement)->item(0)->nodeValue; 
        
        return mb_substr($number, 1);
    }
         
    /**
     * returns the boardId
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return integer
     */
    private function findBoardIdInDom($xpath, $postElement) {
        
        $link = $xpath->query('//ul[@class="crumbs"]/li//a', $postElement)->item(1)->getAttribute('href');
        
        preg_match('/id\=(\d+)/', $link, $boardId);
        
        return $boardId[1];
    }
    
    /**
     * returns the threadId
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return integer
     */
    private function findThreadIdInDom($xpath, $postElement) {
        
        $link = $xpath->query('//ul[@class="crumbs"]/li//a', $postElement)->item(2)->getAttribute('href');
        
        preg_match('/id\=(\d+)/', $link, $threadId);
        
        return $threadId[1];
    }
    
    /**
     * returns the threadTitle
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return string
     */
    private function findTitleInDom($xpath, $postElement) {
        
        $title = $xpath->query('//ul[@class="crumbs"]/li//a', $postElement)->item(2)->nodeValue;
        
        return $title;
    }
    
    /**
     * returns the postId
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return integer
     */
    private function findPostIdInDom($xpath, $postElement) {
        
        $link = $xpath->query('./h2//a', $postElement)->item(0)->getAttribute('href');
        
        preg_match('/pid\=(\d+)/', $link, $postId);
        
        return $postId[1];
    }
    
    /**
     * returns the postTime
     * 
     * @param \DOMXPath $xpath
     * @param \DOMNode $postElement
     * @return \DateTime
     */
    private function findTimeInDom($xpath, $postElement) {
        
        $time = $xpath->query('./h2//a', $postElement)->item(0)->nodeValue;
        
        $postDateTime = new \DateTime($time . ' +0100');
        
        //remodifying the today and 'wrong' date
        if (date('H', time()) == 23) {
            
            $postDateTime->modify('+1 day');
        } 
        
        $postDateTime->setTimezone(new \DateTimeZone('UTC'));
        
        return $postDateTime;
    }
    
    /**
     * reformats the header to make the html readable for DomDocument
     * 
     * @param string $html
     * @return string
     */
    private function makeHtmlReadable($html) {
        
        return str_replace('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">', '<html>', $html);
    }
}
