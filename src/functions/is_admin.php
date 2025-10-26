<?php

function is_admin(){
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 'true'){
        return true;
    }
    else {
        return false;
    }
}