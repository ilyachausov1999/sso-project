<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'required' => ':attribute является обязательным полем.',
    'max' => [
        'string' => 'В строке поля :attribute должно содержаться не более :max символов.',
    ],
    'min' => [
        'string' => 'В строке поля :attribute должно содержаться не менее :min символов.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
    ],

    'array' => 'Поле :attribute должно быть массивом.',
    'string' => 'Поле :attribute должно являться строкой.',
    'integer' => 'Поле :attribute должно являться числом.',
    'boolean' => 'Поле :attribute должно являться true, либо false',
    'email' => 'Поле :attribute должно быть валидным email адресом.',
    'date' => 'Поле :attribute должно содержать действительную дату.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'exists' => 'Указанный :attribute некерректный, или не существует.',
    'in' => 'Указанное значение :attribute не валидно.',
    'unique' => 'Указанный :attribute не уникальный и уже кем-то используется.',
    'same' => 'Поле :attribute должно совпадать с полем пароль.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
