<?php

function mergeSort(array $array, string $key): array
{
    if (count($array) <= 1) return $array;
    $middle = (int)(count($array) / 2);
    $left = mergeSort(array_slice($array, 0, $middle), $key);
    $right = mergeSort(array_slice($array, $middle), $key);
    return mergeArrays($left, $right, $key);
}

function mergeArrays(array $left, array $right, string $key): array
{
    $result = [];
    while (!empty($left) && !empty($right)) {
        $l = strtolower((string)$left[0][$key]);
        $r = strtolower((string)$right[0][$key]);
        if ($l <= $r) $result[] = array_shift($left);
        else          $result[] = array_shift($right);
    }
    return array_merge($result, $left, $right);
}

function linearSearch(array $array, string $keyword, string $key): array
{
    $results = [];
    $keyword = strtolower(trim($keyword));
    foreach ($array as $item) {
        $value = strtolower((string)$item[$key]);
        if ($keyword === '' || str_contains($value, $keyword)) $results[] = $item;
    }
    return $results;
}

function validate_contact(string $name, string $email, string $phone): array
{
    $errors = [];
    if (trim($name) === '') $errors[] = "Full name is required.";
    if (trim($email) !== '' && !filter_var(trim($email), FILTER_VALIDATE_EMAIL))
        $errors[] = "Please enter a valid email address.";
    if (trim($phone) !== '' && !preg_match('/^[0-9+\-\s()]{6,20}$/', trim($phone)))
        $errors[] = "Please enter a valid phone number.";
    return $errors;
}