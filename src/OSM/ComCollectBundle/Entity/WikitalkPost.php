<?php

namespace OSM\ComCollectBundle\Entity;

/**
 * WikitalkPost
 */
class WikitalkPost
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $authorName;

    /**
     * @var \DateTime
     */
    private $createdTime;

    /**
     * @var integer
     */
    private $threadId;

    /**
     * @var integer
     */
    private $parentId;

    /**
     * @var \OSM\ComCollectBundle\Entity\WikitalkThread
     */
    private $thread;

    /**
     * @var \OSM\ComCollectBundle\Entity\WikitalkPost
     */
    private $parent;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return WikitalkPost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set authorName
     *
     * @param string $authorName
     *
     * @return WikitalkPost
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get authorName
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set createdTime
     *
     * @param \DateTime $createdTime
     *
     * @return WikitalkPost
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * Get createdTime
     *
     * @return \DateTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set threadId
     *
     * @param integer $threadId
     *
     * @return WikitalkPost
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * Get threadId
     *
     * @return integer
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return WikitalkPost
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set thread
     *
     * @param \OSM\ComCollectBundle\Entity\WikitalkThread $thread
     *
     * @return WikitalkPost
     */
    public function setThread(\OSM\ComCollectBundle\Entity\WikitalkThread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \OSM\ComCollectBundle\Entity\WikitalkThread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set parent
     *
     * @param \OSM\ComCollectBundle\Entity\WikitalkPost $parent
     *
     * @return WikitalkPost
     */
    public function setParent(\OSM\ComCollectBundle\Entity\WikitalkPost $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \OSM\ComCollectBundle\Entity\WikitalkPost
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * @var \DateTime
     */
    private $collectedTime;


    /**
     * Set collectedTime
     *
     * @param \DateTime $collectedTime
     *
     * @return WikitalkPost
     */
    public function setCollectedTime($collectedTime)
    {
        $this->collectedTime = $collectedTime;

        return $this;
    }

    /**
     * Get collectedTime
     *
     * @return \DateTime
     */
    public function getCollectedTime()
    {
        return $this->collectedTime;
    }
    /**
     * @var integer
     */
    private $layer;


    /**
     * Set layer
     *
     * @param integer $layer
     *
     * @return WikitalkPost
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;

        return $this;
    }

    /**
     * Get layer
     *
     * @return integer
     */
    public function getLayer()
    {
        return $this->layer;
    }
    /**
     * @var string
     */
    private $userName;


    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return WikitalkPost
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add child
     *
     * @param \OSM\ComCollectBundle\Entity\WikitalkPost $child
     *
     * @return WikitalkPost
     */
    public function addChild(\OSM\ComCollectBundle\Entity\WikitalkPost $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \OSM\ComCollectBundle\Entity\WikitalkPost $child
     */
    public function removeChild(\OSM\ComCollectBundle\Entity\WikitalkPost $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
}
