<?php

namespace OSM\ComCollectBundle\Utils;

use \OSM\ComCollectBundle\Entity\WikitalkThread;
use \OSM\ComCollectBundle\Entity\WikitalkPost;

class WikitalkSource
{    
    private $feedLink = 'https://wiki.openstreetmap.org/w/api.php?hidebots=1&namespace=1&days=7&limit=50&action=feedrecentchanges&feedformat=rss';
    
    private $threads = [];
    private $lastThreadNom = 0;
    private $posts = [];
    private $postNom = 0;
    private $lastPostNomInLayer = [];
    private $pageName;
    
    public function getNew(\DateTime $lastCollectTime, $maxNew = 5) {
        
        $pages = [];
        
        foreach($this->getUpdatedPageLinks($lastCollectTime, $maxNew) as $pageLink) {
            
            $page['name'] = $this->getPageName($pageLink);
            $page['posts'] = $this->getPostsByPageLink($pageLink);
            
            $pages[] = $page;
        }
        
        return $pages;
    }
    
    private function getUpdatedPageLinks(\DateTime $lastCollectTime, $pageCount) {
     
        $rss = file_get_contents($this->feedLink);        
        $feed = new \SimpleXmlElement($rss);
        
        $pageLinks = [];
        foreach ($feed->channel->item as $entry) { 
            
            //get pubdate of item
            $pubDate = new \DateTime($entry->pubDate);
            $pubDate->setTimeZone(new \DateTimeZone('UTC'));
            
            //only collect unseen entries
            if ($pubDate < $lastCollectTime) {
                
                break;
            }
            
            //only ProposalTalks
            if (!preg_match('!^Talk:Proposed features(.*)!', $entry->title)) {
                
                continue;
            }
            
            //ignore already listed pageLinks
            if (in_array($entry->comments->__toString(), $pageLinks)) {
            
                continue;
            }
                
            //add pageLink to list
            $pageLinks[] = $entry->comments->__toString();
            
            //stop collecting after given count
            if ($pageCount-- == 0) {
                
                 break;
            }
        }
        
        return $pageLinks;
    }
    
    private function getPostsByPageLink($pageLink) {
        
        $content = $this->getPageContent($pageLink);
        
        //loop each line
        foreach ($content as $line) {
            
            //start new thread
            if ($title = $this->foundNewThread($line)) {
                
                $this->startNewThread($title);
                
                continue;
            }
            
            //new post
            if (!empty($line)) {
                
                $data = $this->getLineData($line);
                
                if ($this->postNom) { //TODO: create methods for lastpost, dann ist diese if-condition unnötig
                    
                    $lastPost = $this->posts[$this->postNom];

                    //if last post has same layer but no userrName then merge the linedata
                    if (!$lastPost->getUserName() AND $lastPost->getLayer() == $data['layer']) {

                        $this->addLineDataToPost($data, $this->postNom);

                        continue;
                    }
                }
                
                $this->startNewPost($data);
                
                continue;
            }
        }
        
        return array_merge_recursive($this->threads, $this->posts);
    }

    private function getLineData($line) {
        
        $data = [];
        
        $data['layer'] = $this->getLineLayer($line);
        $data['thread'] = $this->getThread('LAST');
        $data['userName'] = $this->getUserNameFromLine($line);
        $data['content'] = $this->getLineContent($line);
        $data['createdTime'] = $this->getTimeFromLine($line);
        $data['collectedTime'] = new \DateTime('now');
        $data['parent'] = $this->getParentPost($data['layer']);
        
        return $data;
    }
    
    private function addLineDataToPost($data, $postNom) {
        
        $post = $this->posts[$postNom];
        
        $post->setContent($post->getContent() . "\n" .$data['content'] );
        $post->setUserName($data['userName']);
        $post->setCreatedTime($data['createdTime']);
        
        $this->posts[$postNom] = $post; //todo: work with refs to object
    }
    
    private function startNewPost($data) {
                
        $this->postNom++;
        
        $this->lastPostNomInLayer[$data['layer']] = $this->postNom;
        
        $post = new WikitalkPost();
        $post->setThread($data['thread']);
        $post->setLayer($data['layer']);
        $post->setUserName($data['userName']);
        $post->setContent($data['content']);
        $post->setCreatedTime($data['createdTime']);        
        $post->setCollectedTime($data['collectedTime']);
        $post->setParent($data['parent']);

        $this->posts[$this->postNom] = $post;
    }
    
    private function getParentPost($lineLayer) {
        
        //loop layers above
        for ($i=$lineLayer-1; $i >= 0; $i--) {
        
            //next defined layer
            if (isset($this->lastPostNomInLayer[$i])) {
                
                return $this->posts[$this->lastPostNomInLayer[$i]];
            }
        }
        
        return NULL;
    }
    
    private function getThread($threadNom) {
                
        //post is not threaded
        if (!$this->lastThreadNom) {

            $this->startNewThread('__base__');
        }
        
        $threadNom = $threadNom == 'LAST' ? $this->lastThreadNom : $threadNom;
        
        return $this->threads[$threadNom];
    }
    
    private function startNewThread($title) {
        
        $this->lastThreadNom++;
        
        $thread = new WikitalkThread();
        $thread->setTitle($title);
        $thread->setPageName($this->pageName);
        
        $this->threads[$this->lastThreadNom] = $thread;
        
        return $this->lastThreadNom;
    }
    
    private function getPageContent($pageLink) {
        
        $dom = new \DOMDocument();
        $dom->load($this->getEditPageLink($pageLink)); 
        
        $textarea = $dom->getElementsByTagName('textarea');
        
        //no textarea content found
        if (!$textarea->length) {
            
            return [];
        }
        
        $content = $textarea->item(0)->nodeValue;
        $content = explode("\n", $content);
        
        return $content;
    }
    
    private function foundNewThread($line) {
        
        $newthread = preg_match('!^==(.*)==$!', $line, $topic);
        
        if (!$newthread) {
            
            return false;
        }
        
        return trim($topic[1]);
    }
    
    private function getPageName($pageLink) {
        
        preg_match('!https\:\/\/wiki\.openstreetmap\.org\/wiki\/(.*)!', $pageLink, $pageName);
        
        $this->pageName = $pageName[1];
        
        return $this->pageName;
    }
    
    private function getEditPageLink($pageLink) {
        
        #return 'https://wiki.openstreetmap.org/w/index.php?title=User_talk:Hakuch&action=edit';
        
        $pageLink = 'https://wiki.openstreetmap.org/w/index.php?title=' . $this->getPageName($pageLink) . '&action=edit';
        
        return $pageLink;
    }
    
    private function getUserNameFromLine($line) {
        
        preg_match('!\[\[User\:(.*?)\|(.*?)\]\]!', $line, $userName);
        
        if (!empty($userName)) {
        
            return $userName[1];
        }
        
        return NULL;
    }
    
    private function getTimeFromLine($line) {
        
        preg_match('!([0-1][0-9]|[2][0-3]):([0-5][0-9]),(.*)\(.*\)!', $line, $time); 
                
        if (empty($time)) {
        
            return NULL;
        }
        
        if (!\DateTime::createFromFormat('H\:i, d F Y (e', $time[0])) {
                
            return NULL;
        }
        
        $datetime = new \DateTime($time[0]);
        $datetime->setTimezone(new \DateTimeZone('UTC'));
        
        return $datetime;
        
    }
    
    private function getLineContent($line) {
        
        preg_match('!^(:{0,})(.*)((\[\[User\:(.*))|)!', $line, $lineContent); 
        
        if (!empty($lineContent)) {
        
            return trim($lineContent[2]);
        }
        
        return '';
    }
    
    private function getLineLayer($line) {
        
        preg_match('!^:{1,}!', $line, $lineLayer);
        
        if (!empty($lineLayer)) {
        
            return substr_count($lineLayer[0], ':');
        }
        
        return 0;
    }    
}
