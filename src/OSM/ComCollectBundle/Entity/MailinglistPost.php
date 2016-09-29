<?php

namespace OSM\ComCollectBundle\Entity;

/**
 * MailinglistPost
 */
class MailinglistPost
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var string
     */
    private $fromAddress;

    /**
     * @var \DateTime
     */
    private $fromTime;

    /**
     * @var \DateTime
     */
    private $collectedTime;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $referenceId;

    /**
     * @var integer
     */
    private $listId;


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
     * Set subject
     *
     * @param string $subject
     *
     * @return MailinglistPost
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return MailinglistPost
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set fromName
     *
     * @param string $fromName
     *
     * @return MailinglistPost
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * Get fromName
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Set fromAddress
     *
     * @param string $fromAddress
     *
     * @return MailinglistPost
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    /**
     * Get fromAddress
     *
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * Set fromTime
     *
     * @param \DateTime $fromTime
     *
     * @return MailinglistPost
     */
    public function setFromTime($fromTime)
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    /**
     * Get fromTime
     *
     * @return \DateTime
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    /**
     * Set collectedTime
     *
     * @param \DateTime $collectedTime
     *
     * @return MailinglistPost
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
     * Set messageId
     *
     * @param string $messageId
     *
     * @return MailinglistPost
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Get messageId
     *
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Set referenceId
     *
     * @param string $referenceId
     *
     * @return MailinglistPost
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * Get referenceId
     *
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * Set listId
     *
     * @param integer $listId
     *
     * @return MailinglistPost
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Get listId
     *
     * @return integer
     */
    public function getListId()
    {
        return $this->listId;
    }
    /**
     * @var string
     */
    private $inreplytoId;


    /**
     * Set inreplytoId
     *
     * @param string $inreplytoId
     *
     * @return MailinglistPost
     */
    public function setInreplytoId($inreplytoId)
    {
        $this->inreplytoId = $inreplytoId;

        return $this;
    }

    /**
     * Get inreplytoId
     *
     * @return string
     */
    public function getInreplytoId()
    {
        return $this->inreplytoId;
    }
    /**
     * @var \OSM\ComCollectBundle\Entity\MailinglistThread
     */
    private $thread;


    /**
     * Set thread
     *
     * @param \OSM\ComCollectBundle\Entity\MailinglistThread $thread
     *
     * @return MailinglistPost
     */
    public function setThread(\OSM\ComCollectBundle\Entity\MailinglistThread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \OSM\ComCollectBundle\Entity\MailinglistThread
     */
    public function getThread()
    {
        return $this->thread;
    }
    /**
     * @var string
     */
    private $content;


    /**
     * Set content
     *
     * @param string $content
     *
     * @return MailinglistPost
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
}
