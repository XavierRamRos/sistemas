<?php

function hashPassword($password) {

    $options = [
        'cost' => 12
    ];
    return password_hash($password, PASSWORD_DEFAULT, $options);
}


function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}


function needsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
}