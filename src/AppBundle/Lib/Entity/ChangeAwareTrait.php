<?php


namespace AppBundle\Lib\Entity;


trait ChangeAwareTrait
{
    /**
     * Array holding the clean data for the object.
     *
     * @var array
     */
    protected $cleanData = [];

    public function getCurrentValue($key)
    {
        $m = sprintf("get%s",str_replace(' ','',ucwords(str_replace('_',' ',$key))));

        if (method_exists($this, $m)) {
            return $this->$m();
        }

        $m = sprintf("is%s",str_replace(' ','',ucwords(str_replace('_',' ',$key))));

        if (method_exists($this, $m)) {
            return $this->$m();
        }

        if (!property_exists($this, $key)) {
            throw new \BadMethodCallException('Class: '.get_class($this).' has no property '.$key);
        }

        return $this->$key;
    }

    /**
     * Loads the data for the keys specified in $this->cleanDataMap into the
     *
     * @return array
     */
    public function initCleanData() :array
    {
        $this->cleanData = [];

        foreach ($this->getTrackList() AS $k) {
            $this->cleanData[$k] = $this->getCurrentValue($k);
        }

        return $this->cleanData;
    }

    /**
     * Returns an array of changes for the given object.
     *
     * @return ChangeList
     * @throws NotTrackedException
     */
    public function getChanges() :ChangeList
    {
        $changes = [];
        foreach ($this->getTrackList() AS $k) {
            if (!$this->isChanged($k)) {
                continue;
            }

            $changes[$k] = $this->getChange($k);
        }

        return new ChangeList($changes);
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws NotTrackedException
     */
    public function isChanged(string $key) :bool
    {
        if (!$this->isTracked($key)) {
            throw new NotTrackedException('Key: '.$key.' is not tracked');
        }

        if (!array_key_exists($key, $this->cleanData)) {
            return (null !== $this->$key);
        }

        $currValue = $this->getCurrentValue($key);

        return $this->cleanData[$key] != $currValue;
    }

    /**
     * @return array
     */
    public function getCleanData() :array
    {
        return $this->cleanData;
    }

    /**
     * @param string $key
     * @return mixed|null
     * @throws NotTrackedException
     */
    public function getCleanValue(string $key)
    {
        if (!$this->isTracked($key)) {
            throw new NotTrackedException('Key: '.$key.' is not tracked');
        }

        if (!array_key_exists($key, $this->cleanData)) {
            return null;
        }

        return $this->cleanData[$key];
    }

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
    public function getChange(string $key) :Change
    {
        if (!$this->isTracked($key)) {
            throw new NotTrackedException('Key: '.$key.' is not tracked');
        }

        $old = $this->cleanData[$key] ?? null;
        $new = $this->getCurrentValue($key);

        return new Change($key, $old, $new);
    }

    /**
     * Checks if the $key is tracked.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isTracked(string $key) :bool
    {
        if ($key === '' || !is_string($key)) {
            throw new \BadMethodCallException('Key cannot be empty');
        }

        return (in_array($key, $this->getTrackList(), true));
    }
}