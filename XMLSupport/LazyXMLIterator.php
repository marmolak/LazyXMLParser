<?php
namespace XMLSupport;

class LazyXMLIterator implements \Iterator
{
    var $xml_file = NULL;
    var $xml = NULL;
    var $root_element = array();
    var $si = NULL;
    var $position = 0;

    public function __construct($xml_file, $root_element)
    {
        $this->xml_file = $xml_file;
        $this->root_element = $root_element;
    }

    // Open file and find first occurence of node
    private function init()
    {
        $this->position = 0;
        $this->si = NULL;
        $this->xml = new \XMLReader();
        $ok = $this->xml->open('file://' . $this->xml_file);
        if (!$ok) {
            $this->xml = NULL;
            throw new Exception("Iterator error: Unable to open file: {$this->xml_file}");
        }

        while ($this->xml->read() && $this->xml->name !== $this->root_element);
        $this->si = \simplexml_load_string($this->xml->readOuterXML(), 'SimpleXMLElement', LIBXML_NOENT | LIBXML_NOCDATA | LIBXML_COMPACT);
    }

    public function rewind()
    {
        if ($this->xml != NULL) {
            $this->xml->close();
        }

        $this->init();
    }

    public function next()
    {
        $this->xml->next($this->root_element);
        $this->si = \simplexml_load_string($this->xml->readOuterXML(), 'SimpleXMLElement', LIBXML_NOENT | LIBXML_NOCDATA | LIBXML_COMPACT);
        ++$this->position;
    }

    public function current()
    {
        return $this->si;
    }

    public function valid()
    {
        $valid = ($this->xml->name == $this->root_element);
        return $valid;
    }

    public function __destruct()
    {
        if ($this->xml == NULL) { return; }

        $this->xml->close();
    }

    public function key()
    {
        return $this->position;
    }
}
