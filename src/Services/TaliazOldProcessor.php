<?php

namespace App\Services;

class TaliazOldProcessor {

    /**
     * @var array
     */
    protected $mapper;

    protected $objectsHandler = [
        'DateTime' => 'dateTimeProcessor',
    ];

    /**
     * Set the mapper.
     *
     * Since the entities properties are defined in camel case we might need to
     * alter the properties from camel case to normal formatting or maybe change
     * a property to a name we desire. By setting the mapper we can have this
     * privilege. Example:
     *
     * @code
     *  ['userId' => 'user_id']
     * @endcode
     *
     * @param mixed $mapper
     *  The mapper.
     *
     * @return TaliazOldProcessor
     */
    public function setMapper($mapper): TaliazOldProcessor {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * Process the records.
     *
     * In addition to the mapper above, we need to change all the values to a
     * string so the predictix could handle the values.
     *
     * @param $records
     *  The records we need to change.
     *
     * @return TaliazOldProcessor
     */
    public function processRecords(&$records): TaliazOldProcessor {
        foreach ($records as &$record) {
            $this->processRecord($record);
        }

        return $this;
    }

    public function processRecord(&$record) {
        foreach (get_object_vars($record) as $property => $value) {

            if ($property == 'id') {
                // We don't need to convert the id to string.
                continue;
            }

            if (!is_null($value)) {
                $record->{$property} = $this->handleValue($value);
            }

            if (!empty($this->mapper[$property])) {
                $value = $record->{$property};
                unset($record->{$property});
                $record->{$this->mapper[$property]} = $value;
            }
        }
    }

    /**
     * Convert value from any thing to string.
     *
     * For non primitive values(string, integer, float etc. etc.) we have the
     * objectsHandler property which indicate which method need to handle that
     * value.
     *
     * @param $value
     *  The value we need to change.
     * @return string
     */
    protected function handleValue($value): string {

        if (is_object($value)) {
            return $this->{$this->objectsHandler[get_class($value)]}($value);
        }

        return (string) $value;
    }

    /**
     * Changing a date time to string.
     *
     * @param \DateTime $value
     *  The date time object.
     *
     * @return string
     *  The date.
     */
    protected function dateTimeProcessor(\DateTime $value): string {
        return $value->format('Y-m-d H:i:s');
    }

}
