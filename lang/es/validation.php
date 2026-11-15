<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'min' => ['numeric' => 'El campo :attribute debe ser al menos :min.', 'string' => 'El campo :attribute debe tener al menos :min caracteres.'],
    'max' => ['numeric' => 'El campo :attribute no debe superar :max.', 'string' => 'El campo :attribute no debe superar :max caracteres.'],
    'between' => ['numeric' => 'El campo :attribute debe estar entre :min y :max.'],
    'unique' => 'El campo :attribute ya está registrado.',
];
