<?php

namespace OSM\ComCollectBundle\Entity;

/**
 * MailinglistThread
 */
class MailinglistThread
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $posts;

    /**
     * @var \OSM\ComCollectBundle\Entity\MailinglistList
     */
    private $list;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return MailinglistThread
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add post
     *
     * @param \OSM\ComCollectBundle\Entity\MailinglistPost $post
     *
     * @return MailinglistThread
     */
    public function addPost(\OSM\ComCollectBundle\Entity\MailinglistPost $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \OSM\ComCollectBundle\Entity\MailinglistPost $post
     */
    public function removePost(\OSM\ComCollectBundle\Entity\MailinglistPost $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set list
     *
     * @param \OSM\ComCollectBundle\Entity\MailinglistList $list
     *
     * @return MailinglistThread
     */
    public function setList(\OSM\ComCollectBundle\Entity\MailinglistList $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return \OSM\ComCollectBundle\Entity\MailinglistList
     */
    public function getList()
    {
        return $this->list;
    }
}
