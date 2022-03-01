<?php

namespace App;

use XMLWriter;

class Array2Xml
{
    private $version;

    public function __construct($xmlVersion = '1.0'){
        $this->version = $xmlVersion;
    }

    public function buildXML($data, $startElement = 'data')
    {
        if (!is_array($data)) {
            $err = 'Invalid variable type supplied, expected array not found on line ' . __LINE__ . ' in Class: ' . __CLASS__ . ' Method: ' . __METHOD__;
            trigger_error($err);
            return false; //return false error occurred
        }
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startDocument($this->version);
        $xml->setIndent(true);
        $xml->setIndentString("\t");
        $xml->startElement($startElement);

        $this->writeAttr($xml, $data);
        $this->writeEl($xml, $data);

        $xml->endElement(); //write end element
        //returns the XML results
        return $xml->outputMemory(true);
    }

    protected function writeAttr(XMLWriter $xml, $data){
        if (is_array($data)) {
            $nonAttributes = array();
            foreach ($data as $key => $val) {
                //handle an attribute with elements
                if ($key[0] == '@') {
                    $xml->writeAttribute(substr($key, 1), $val);
                } else if ($key[0] == '%') {
                    if (is_array($val)) $nonAttributes = $val;
                    else $xml->text($val);
                } elseif ($key[0] == '*') {
                    $xml->writeRaw($val);
                } elseif ($key[0] == '#') {
                    if (is_array($val)) $nonAttributes = $val;
                    else {
                        $xml->startElement(substr($key, 1));
                        $xml->writeCData($val);
                        $xml->endElement();
                    }
                } //ignore normal elements
                else $nonAttributes[$key] = $val;
            }
            return $nonAttributes;
        } else return $data;
    }

    protected function writeEl(XMLWriter $xml, $data){
        foreach ($data as $key => $value) {
            if (is_array($value) && !$this->isAssoc($value)) { //numeric array
                foreach ($value as $itemValue) {
                    if (is_array($itemValue)) {
                        $xml->startElement($key);
                        $itemValue = $this->writeAttr($xml, $itemValue);
                        $this->writeEl($xml, $itemValue);
                        $xml->endElement();
                    } else {
                        $itemValue = $this->writeAttr($xml, $itemValue);
                        $xml->writeElement($key, "$itemValue");
                    }
                }
            } else if (is_array($value)) { //associative array
                $xml->startElement($key);
                $value = $this->writeAttr($xml, $value);
                $this->writeEl($xml, $value);
                $xml->endElement();
            } elseif($key != '*') { //scalar
                $value = $this->writeAttr($xml, $value);
                $xml->writeElement($key, "$value");
            }
        }
    }

    protected function isAssoc($array){
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

}
