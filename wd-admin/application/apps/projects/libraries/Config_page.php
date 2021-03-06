<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Config_page
{

    public function inputs()
    {
        $input = array();
        $input[] = ['name' => 'Text', 'value' => 'text'];
        $input[] = ['name' => 'Textarea', 'value' => 'textarea'];
        $input[] = ['name' => 'File', 'value' => 'file'];
        $input[] = ['name' => 'Multifile', 'value' => 'multifile'];
        $input[] = ['name' => 'Password', 'value' => 'password'];
        $input[] = ['name' => 'Checkbox', 'value' => 'checkbox'];
        $input[] = ['name' => 'Select', 'value' => 'select'];
        $input[] = ['name' => 'Hidden', 'value' => 'hidden'];

        return $input;
    }

    public function types()
    {
        $input = array();
        $input[] = ['type' => 'integer', 'constraint' => 11];
        $input[] = ['type' => 'char'];
        $input[] = ['type' => 'varchar', 'constraint' => 255];
        $input[] = ['type' => 'tinytext'];
        $input[] = ['type' => 'text'];
        $input[] = ['type' => 'mediumText'];
        $input[] = ['type' => 'longtext'];
        $input[] = ['type' => 'date', 'constraint' => ''];
        $input[] = ['type' => 'datetime', 'constraint' => ''];
        $input[] = ['type' => 'year', 'constraint' => ''];
        $input[] = ['type' => 'time', 'constraint' => ''];
        $input[] = ['type' => 'timestamp', 'constraint' => ''];
        $input[] = ['type' => 'float', 'constraint' => ''];
        $input[] = ['type' => 'double', 'constraint' => ''];

        return $input;
    }
}