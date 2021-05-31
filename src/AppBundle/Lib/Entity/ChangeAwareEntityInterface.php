<?php


namespace AppBundle\Lib\Entity;


interface ChangeAwareEntityInterface
{

    /**
     * Should return the keys to track
     * @return array
     */
    public function getTrackList() :array;

    /**
     * Initializes the behaviour and stores away the clean data.
     *
     * @return self
     */
    public function initCleanData();

    /**
     * Returns an array containing the clean data for the tracked elements, if $key
     * is provided only the data for key is returned. If key is not tracked
     * null is returned.
     *
     * @param string $key
     *
     * @return array
     */
    public function getCleanData() :array;

    /**
     * Returns
     * @param string $key
     *
     * @return mixed
     */
    public function getCleanValue(string $key);

    /**
     * Returns an array of the changes of the tracked data
     *
     * @return ChangeList
     */
    public function getChanges() :ChangeList;

    /**
     * Returns an array containing the changes for the element specified by $key,
     * the first element contains the clean data and the second element
     * contains the changes.
     *
     * @param string $key
     *
     * @return Change
     * @throws NotTrackedException
     */
    public function getChange(string $key) :Change;

    /**
     * Check if the value of $key has changed.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isChanged(string $key) :bool;

    /**
     * Checks if the $key is tracked.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isTracked(string $key) :bool;
}