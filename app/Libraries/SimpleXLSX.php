<?php

namespace App\Libraries;

class SimpleXLSX
{
    const SCHEMA_REL_OFFICEDOCUMENT = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
    const SCHEMA_REL_SHAREDSTRINGS  = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
    const SCHEMA_REL_WORKSHEET      = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet';

    private $workbook;
    private $sheets = [];
    private $sharedStrings = [];
    private $error = false;

    public static function parse($filename)
    {
        $xlsx = new self();
        return $xlsx->_parse($filename) ? $xlsx : false;
    }

    public static function parseError()
    {
        return (new self())->error;
    }

    public function rows($worksheetIndex = 0)
    {
        if (!isset($this->sheets[$worksheetIndex])) {
            return [];
        }
        return $this->sheetData($this->sheets[$worksheetIndex]);
    }

    private function _parse($filename)
    {
        $zip = new \ZipArchive;
        if (true !== $zip->open($filename)) {
            $this->error = 'Unable to open ' . $filename;
            return false;
        }

        $index = [];
        // Read relations to find workbook
        if (($relations = $zip->getFromName('_rels/.rels')) &&
            ($xml = simplexml_load_string($relations))
        ) {
            foreach ($xml->Relationship as $rel) {
                if ($rel['Type'] == self::SCHEMA_REL_OFFICEDOCUMENT) {
                    $index['workbook'] = (string)$rel['Target'];
                }
            }
        }

        if (empty($index['workbook'])) {
            $this->error = 'Workbook not found';
            return false;
        }

        $this->workbook = simplexml_load_string($zip->getFromName($index['workbook']));

        // Get sharedStrings
        $sharedStringsPath = dirname($index['workbook']) . '/sharedStrings.xml';
        if (($xml = $zip->getFromName($sharedStringsPath))) {
            $sxml = simplexml_load_string($xml);
            foreach ($sxml->si as $si) {
                $this->sharedStrings[] = (string)$si->t;
            }
        }

        // Get worksheets
        foreach ($this->workbook->sheets->sheet as $sheet) {
            $sheetPath = dirname($index['workbook']) . '/' . $sheet['name'] . '.xml';
            $sheetPathRels = dirname($index['workbook']) . '/_rels/' . basename($index['workbook']) . '.rels';

            if (($rels = $zip->getFromName($sheetPathRels)) &&
                ($xml = simplexml_load_string($rels))
            ) {
                foreach ($xml->Relationship as $rel) {
                    if ($rel['Type'] == self::SCHEMA_REL_WORKSHEET) {
                        $this->sheets[] = simplexml_load_string($zip->getFromName(dirname($index['workbook']) . '/' . $rel['Target']));
                    }
                }
            }
        }

        $zip->close();
        return true;
    }

    private function sheetData($sheet)
    {
        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $r = [];
            foreach ($row->c as $c) {
                $t = (string)$c['t'];
                $v = (string)$c->v;
                if ($t == 's') {
                    $v = $this->sharedStrings[(int)$v];
                }
                $r[] = $v;
            }
            $rows[] = $r;
        }
        return $rows;
    }
}
