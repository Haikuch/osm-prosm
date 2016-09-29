<?php

namespace AppBundle\Entity;

/**
 * Link
 */
class Link
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $localChannel;

    /**
     * @var integer
     */
    private $localReference;

    /**
     * @var string
     */
    private $remoteChannel;

    /**
     * @var string
     */
    private $remoteReference;


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
     * Set localChannel
     *
     * @param string $localChannel
     *
     * @return Link
     */
    public function setLocalChannel($localChannel)
    {
        $this->localChannel = $localChannel;

        return $this;
    }

    /**
     * Get localChannel
     *
     * @return string
     */
    public function getLocalChannel()
    {
        return $this->localChannel;
    }

    /**
     * Set localReference
     *
     * @param integer $localReference
     *
     * @return Link
     */
    public function setLocalReference($localReference)
    {
        $this->localReference = $localReference;

        return $this;
    }

    /**
     * Get localReference
     *
     * @return integer
     */
    public function getLocalReference()
    {
        return $this->localReference;
    }

    /**
     * Set remoteChannel
     *
     * @param string $remoteChannel
     *
     * @return Link
     */
    public function setRemoteChannel($remoteChannel)
    {
        $this->remoteChannel = $remoteChannel;

        return $this;
    }

    /**
     * Get remoteChannel
     *
     * @return string
     */
    public function getRemoteChannel()
    {
        return $this->remoteChannel;
    }

    /**
     * Set remoteReference
     *
     * @param string $remoteReference
     *
     * @return Link
     */
    public function setRemoteReference($remoteReference)
    {
        $this->remoteReference = $remoteReference;

        return $this;
    }

    /**
     * Get remoteReference
     *
     * @return string
     */
    public function getRemoteReference()
    {
        return $this->remoteReference;
    }
}

