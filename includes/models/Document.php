<?php

/**
 * Class Document
 */
class Document
{
    /** @var string */
    private $documentId;
    /** @var stdClass */
    private $file;

    /**
     * @return string
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     * @return Document
     */
    public function setDocumentId(string $documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * @return stdClass
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param stdClass $file
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}