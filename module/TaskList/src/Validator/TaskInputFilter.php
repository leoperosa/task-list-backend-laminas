<?php

namespace TaskList\Validator;

use Laminas\InputFilter\InputFilter;

class TaskInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'title',
            'required' => true,
            'filters' => [['name' => 'StripTags'], ['name' => 'StringTrim']],
        ]);

        $this->add([
            'name' => 'description',
            'required' => false,
        ]);

        $this->add([
            'name' => 'status',
            'required' => true,
            'validators' => [[
                'name' => 'InArray',
                'options' => ['haystack' => ['pending', 'closed', 'in_progress']],
            ]],
        ]);
    }
}
